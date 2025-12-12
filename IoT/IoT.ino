#include <WiFi.h>
#include "DHT.h"
#include <AccelStepper.h>
#include <PubSubClient.h>

//===KONFIGURASI WIFI===
const char* ssid = "Mifta";
const char* password = "12345678901";

//===KONFIGURASI MQTT BROKER===
const char* mqtt_server = "broker.hivemq.com";
const int mqtt_port = 1883;
const char* mqtt_client_id = "ESP32_Jamur_01";

// ===== TOPIK MQTT =====
const char* topic_suhu = "jamur/suhu";
const char* topic_kelembaban = "jamur/kelembaban";
const char* topic_jarak = "jamur/jarak";
const char* topic_status = "jamur/status";

// ===== KONFIGURASI PIN SENSOR =====
#define DHTPIN 26
#define DHTTYPE DHT22
#define TRIG_PIN 12
#define ECHO_PIN 13

// ===== KONFIGURASI RELAY =====
#define RELAY_POMPA 17
#define RELAY_LAMPU 14
#define RELAY_KIPAS 25

// ===== KONFIGURASI TB6600 =====
#define STEP_PIN 16
#define DIR_PIN 27
#define EN_PIN 5

AccelStepper stepper(AccelStepper::DRIVER, STEP_PIN, DIR_PIN);

// ===== BATAS KONDISI =====
const float SUHU_NORMAL_MIN = 33.0;
const float SUHU_NORMAL_MAX = 34.0;
const float KELEMBABAN_MIN = 86.0;
const float KELEMBABAN_MAX = 87.0;
const int JARAK_JAMUR = 10; // cm

//===OBJEK===
WiFiClient espClient;
PubSubClient client(espClient);
DHT dht(DHTPIN, DHTTYPE);

// ===== STATUS =====
String statusLampu = "OFF";
String statusPompa = "OFF";
String statusKipas = "OFF";
String statusMotor = "OFF";

// ===== VARIABEL WAKTU =====
unsigned long lastSensorRead = 0;
const unsigned long sensorInterval = 1000; // 1 detik

// ===== NILAI SENSOR TERAKHIR =====
float suhu = 0;
float kelembaban = 0;
int jarak = 0;

void setup() {
Serial.begin(115200);
dht.begin();

pinMode(TRIG_PIN, OUTPUT);
pinMode(ECHO_PIN, INPUT);

pinMode(RELAY_POMPA, OUTPUT);
pinMode(RELAY_LAMPU, OUTPUT);
pinMode(RELAY_KIPAS, OUTPUT);

pinMode(EN_PIN, OUTPUT);
digitalWrite(EN_PIN, LOW); // aktifkan TB6600

// Setup motor stepper
stepper.setMaxSpeed(50000000);      // jangan terlalu tinggi
stepper.setAcceleration(100000);   // agar gerak halus

matikanSemua();

//===KONEKSI WIFI===
Serial.println("Menghubungkan wifi...");
WiFi.begin(ssid, password);
while (WiFi.status() != WL_CONNECTED){
delay(500);
Serial.print(".");
}
Serial.print("Wifi Terhubung");
Serial.print("IP Address:");
Serial.println(WiFi.localIP());

//===KONEKSI MQTT===
client.setServer(mqtt_server, mqtt_port);
reconnectMQTT();
}

bool modeSpray = false;
unsigned long sprayStart = 0;
unsigned long lastCheck = 0;

void loop() {
client.loop();
unsigned long currentMillis = millis();

// === BACA SENSOR SETIAP 1 DETIK (non-blocking) ===
if (currentMillis - lastSensorRead >= sensorInterval) {
lastSensorRead = currentMillis;

float newSuhu = dht.readTemperature();
float newKelembaban = dht.readHumidity();
int newJarak = bacaUltrasonik();

if (!isnan(newSuhu) && !isnan(newKelembaban)) {
suhu = newSuhu;
kelembaban = newKelembaban;
jarak = newJarak;

kontrolPerangkat(suhu, kelembaban, jarak);

// === TAMPIL KE SERIAL MONITOR ===
Serial.println("================================");
Serial.print("Suhu: "); Serial.print(suhu); Serial.println(" °C");
Serial.print("Kelembaban: "); Serial.print(kelembaban); Serial.println(" %");
Serial.print("Jarak: "); Serial.print(jarak); Serial.println(" cm");
Serial.println("Status: L:" + statusLampu + " | K:" + statusKipas +
" | P:" + statusPompa + " | M:" + statusMotor);
} else {
Serial.println("Gagal baca DHT22!");
}

}

// Jalankan motor stepper secara halus
stepper.run();
}

// ====== FUNGSI ======
void kontrolPerangkat(float suhu, float kelembaban, int jarak) {
statusLampu = "OFF";
statusKipas = "OFF";
statusPompa = "OFF";

// 1️⃣ Kondisi normal
if (suhu >= SUHU_NORMAL_MIN && suhu <= SUHU_NORMAL_MAX &&
kelembaban >= KELEMBABAN_MIN && kelembaban <= KELEMBABAN_MAX) {
matikanSemua();
}
// 2️⃣ Suhu rendah & Kelembaban normal
else if (suhu <= SUHU_NORMAL_MIN && kelembaban >= KELEMBABAN_MIN && kelembaban <= KELEMBABAN_MAX) {
digitalWrite(RELAY_LAMPU, LOW);
digitalWrite(RELAY_KIPAS, HIGH);
digitalWrite(RELAY_POMPA, HIGH);
statusLampu = "ON"; statusKipas = "OFF"; statusPompa = "OFF";
}

// 2️⃣ Suhu tinggi & Kelembaban normal
else if (suhu >= SUHU_NORMAL_MAX && kelembaban >= KELEMBABAN_MIN && kelembaban <= KELEMBABAN_MAX) {
digitalWrite(RELAY_LAMPU, HIGH);
digitalWrite(RELAY_KIPAS, LOW);
digitalWrite(RELAY_POMPA, HIGH);
statusLampu = "OFF"; statusKipas = "ON"; statusPompa = "OFF";
}

// 3️⃣ Suhu RENDAH KELEMBABAN RENDAH
else if (suhu <= SUHU_NORMAL_MIN && kelembaban <= KELEMBABAN_MIN) {
pompaon();
digitalWrite(RELAY_LAMPU, LOW);
digitalWrite(RELAY_KIPAS, HIGH);
statusLampu = "ON"; statusKipas = "OFF"; statusPompa = "ON";
}
// 4️⃣ SUHU TINGGI KELEMBABAN TINGGI
else if (suhu >= SUHU_NORMAL_MAX && kelembaban >= KELEMBABAN_MAX) {
digitalWrite(RELAY_POMPA, HIGH);
digitalWrite(RELAY_KIPAS, LOW);
digitalWrite(RELAY_LAMPU, HIGH);
statusLampu = "OFF"; statusKipas = "ON"; statusPompa = "OFF";
}

// 4️⃣ SUHU TINGGI KELEMBABAN RENDAH
else if (suhu >= SUHU_NORMAL_MAX && kelembaban <= KELEMBABAN_MIN) {
pompaon();
digitalWrite(RELAY_KIPAS, LOW);
digitalWrite(RELAY_LAMPU, HIGH);
statusLampu = "OFF"; statusKipas = "ON"; statusPompa = "ON";
}

// 4️⃣ KELEMBABAN RENDAH SUHU NORMAL
else if (kelembaban <= SUHU_NORMAL_MIN && suhu >= SUHU_NORMAL_MIN && suhu <= SUHU_NORMAL_MAX) {
pompaon();
digitalWrite(RELAY_KIPAS, HIGH);
digitalWrite(RELAY_LAMPU, HIGH);
statusLampu = "OFF"; statusKipas = "OFF"; statusPompa = "ON";
}

// 4️⃣ KELEMBABAN TINGGI SUHU NORMAL
else if (kelembaban >= SUHU_NORMAL_MAX && suhu >= SUHU_NORMAL_MIN && suhu <= SUHU_NORMAL_MAX) {
digitalWrite(RELAY_POMPA, HIGH);
digitalWrite(RELAY_KIPAS, LOW);
digitalWrite(RELAY_LAMPU, HIGH);
statusLampu = "OFF"; statusKipas = "ON"; statusPompa = "OFF";
}

// 4️⃣ KELEMBABAN TINGGI SUHU RENDAH
else if (kelembaban >= SUHU_NORMAL_MAX && suhu <= SUHU_NORMAL_MIN) {
digitalWrite(RELAY_POMPA, HIGH);
digitalWrite(RELAY_KIPAS, HIGH);
digitalWrite(RELAY_LAMPU, LOW);
statusLampu = "ON"; statusKipas = "OFF"; statusPompa = "OFF";
}

// 5️⃣ Motor panen → TB6600 stepper
if (jarak < JARAK_JAMUR) {
stepper.moveTo(800); // maju
statusMotor = "ON";
} else {
stepper.moveTo(0);   // kembali
statusMotor = "OFF";
}
}

int bacaUltrasonik() {
digitalWrite(TRIG_PIN, LOW);
delayMicroseconds(2);
digitalWrite(TRIG_PIN, HIGH);
delayMicroseconds(10);
digitalWrite(TRIG_PIN, LOW);
long durasi = pulseIn(ECHO_PIN, HIGH, 20000); // timeout 20ms
int jarak = durasi * 0.034 / 2;
return jarak;
}

void reconnectMQTT() {
while (!client.connected()) {
Serial.print("Menghubungkan ke MQTT...");

if (client.connect(mqtt_client_id)) {  
  Serial.println("Terhubung!");  

  // Jika ingin menerima topik dari HP  
  // client.subscribe("jamur/control");  

} else {  
  Serial.print("Gagal, rc=");  
  Serial.print(client.state());  
  Serial.println(" | coba lagi 5 detik...");  
  delay(5000);  
}

}
}

void matikanSemua() {
digitalWrite(RELAY_POMPA, HIGH);
digitalWrite(RELAY_LAMPU, HIGH);
digitalWrite(RELAY_KIPAS, HIGH);
statusLampu = "OFF";
statusPompa = "OFF";
statusKipas = "OFF";
statusMotor = "OFF";
}
void pompaon(){
  digitalWrite(RELAY_POMPA, LOW);
  delay(100);
  digitalWrite(RELAY_POMPA, HIGH);
}