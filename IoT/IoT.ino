#include <WiFi.h>
#include "DHT.h"
#include <AccelStepper.h>
#include <PubSubClient.h>
#include <ArduinoJson.h>
#include "secrests.h"


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
const int JARAK_JAMUR = 10;  // cm

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
const unsigned long sensorInterval = 1000;  // 5 detik

// ===== NILAI SENSOR TERAKHIR =====
float suhu = 0;
float kelembaban = 0;
int jarak = 0;

bool pompaButuh = false;
bool pompaAktif = false;

unsigned long pompaStartTime = 0;
unsigned long pompaCooldownStart = 0;

const unsigned long durasiPompa = 1500;    // 3 detik ON
const unsigned long cooldownPompa = 5000;  // 5 detik OFF

int timeoutKirim = 0;

void kirimStatus(float suhu, float kelembaban) {
  StaticJsonDocument<200> doc;
  doc["device_ulid"] = DEVICE_ULID;
  doc["temperature"] = suhu;
  doc["humidity"] = kelembaban;

  char buffer[200];
  size_t len = serializeJson(doc, buffer);

  char topic[100];
  sprintf(topic, "jamur/%s/monitoring", DEVICE_ULID);
  client.publish(topic, buffer, len);
}

void setup() {
  Serial.begin(115200);
  dht.begin();

  pinMode(TRIG_PIN, OUTPUT);
  pinMode(ECHO_PIN, INPUT);

  pinMode(RELAY_POMPA, OUTPUT);
  pinMode(RELAY_LAMPU, OUTPUT);
  pinMode(RELAY_KIPAS, OUTPUT);

  pinMode(EN_PIN, OUTPUT);
  digitalWrite(EN_PIN, LOW);  // aktifkan TB6600

  // Setup motor stepper
  stepper.setMaxSpeed(50000000);    // jangan terlalu tinggi
  stepper.setAcceleration(100000);  // agar gerak halus

  matikanSemua();

  //===KONEKSI WIFI===
  Serial.println("Menghubungkan wifi...");
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.print("Wifi Terhubung");
  Serial.print("IP Address:");
  Serial.println(WiFi.localIP());

  //===KONEKSI MQTT===
  client.setServer(MQTT_SERVER, MQTT_PORT);
  reconnectMQTT();
}

bool modeSpray = false;
unsigned long sprayStart = 0;
unsigned long lastCheck = 0;

bool manualLampu = false;
bool manualPompaManual = false;
bool manualKipas = false;

String lampuManualState = "AUTO";
String pompaManualState = "AUTO";
String kipasManualState = "AUTO";


void loop() {
  client.loop();
  updatePompa();
  unsigned long currentMillis = millis();

  // === BACA SENSOR SETIAP 5 DETIK (non-blocking) ===
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
      Serial.print("Suhu: ");
      Serial.print(suhu);
      Serial.println(" °C");
      Serial.print("Kelembaban: ");
      Serial.print(kelembaban);
      Serial.println(" %");
      Serial.print("Jarak: ");
      Serial.print(jarak);
      Serial.println(" cm");
      Serial.println("Status: L:" + statusLampu + " | K:" + statusKipas + " | P:" + statusPompa + " | M:" + statusMotor);

      timeoutKirim += 1000;
      if (timeoutKirim >= 5000) {
        kirimStatus(suhu, kelembaban);
        timeoutKirim = 0;
      }
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

    // ===================== MANUAL MODE ======================
  // LAMPU MANUAL
  if (manualLampu) {
    if (lampuManualState == "ON") {
      digitalWrite(RELAY_LAMPU, LOW);
      statusLampu = "ON";
    } else {
      digitalWrite(RELAY_LAMPU, HIGH);
      statusLampu = "OFF";
    }
  }

  // KIPAS MANUAL
  if (manualKipas) {
    if (kipasManualState == "ON") {
      digitalWrite(RELAY_KIPAS, LOW);
      statusKipas = "ON";
    } else {
      digitalWrite(RELAY_KIPAS, HIGH);
      statusKipas = "OFF";
    }
  }

  // POMPA MANUAL
  if (manualPompaManual) {
    if (pompaManualState == "ON") {
      digitalWrite(RELAY_POMPA, LOW);
      statusPompa = "ON";
    } else {
      digitalWrite(RELAY_POMPA, HIGH);
      statusPompa = "OFF";
    }
  }

  // Jika semua manual → otomatis dilewati
  if (manualLampu || manualKipas || manualPompaManual) {
    return;
  }

  // 1️⃣ Kondisi normal
  if (suhu >= SUHU_NORMAL_MIN && suhu <= SUHU_NORMAL_MAX && kelembaban >= KELEMBABAN_MIN && kelembaban <= KELEMBABAN_MAX) {
    matikanSemua();
  }
  // 2️⃣ Suhu rendah & Kelembaban normal
  else if (suhu <= SUHU_NORMAL_MIN && kelembaban >= KELEMBABAN_MIN && kelembaban <= KELEMBABAN_MAX) {
    digitalWrite(RELAY_LAMPU, LOW);
    digitalWrite(RELAY_KIPAS, HIGH);
    digitalWrite(RELAY_POMPA, HIGH);
    statusLampu = "ON";
    statusKipas = "OFF";
    statusPompa = "OFF";
  }

  // 2️⃣ Suhu tinggi & Kelembaban normal
  else if (suhu >= SUHU_NORMAL_MAX && kelembaban >= KELEMBABAN_MIN && kelembaban <= KELEMBABAN_MAX) {
    digitalWrite(RELAY_LAMPU, HIGH);
    digitalWrite(RELAY_KIPAS, LOW);
    digitalWrite(RELAY_POMPA, HIGH);
    statusLampu = "OFF";
    statusKipas = "ON";
    statusPompa = "OFF";
  }

  // 3️⃣ Suhu RENDAH KELEMBABAN RENDAH
  else if (suhu <= SUHU_NORMAL_MIN && kelembaban <= KELEMBABAN_MIN) {
    pompaButuh = true;
    digitalWrite(RELAY_LAMPU, LOW);
    digitalWrite(RELAY_KIPAS, HIGH);
    statusLampu = "ON";
    statusKipas = "OFF";
    statusPompa = "ON";
  }
  // 4️⃣ SUHU TINGGI KELEMBABAN TINGGI
  else if (suhu >= SUHU_NORMAL_MAX && kelembaban >= KELEMBABAN_MAX) {
    digitalWrite(RELAY_POMPA, HIGH);
    digitalWrite(RELAY_KIPAS, LOW);
    digitalWrite(RELAY_LAMPU, HIGH);
    statusLampu = "OFF";
    statusKipas = "ON";
    statusPompa = "OFF";
  }

  // 4️⃣ SUHU TINGGI KELEMBABAN RENDAH
  else if (suhu >= SUHU_NORMAL_MAX && kelembaban <= KELEMBABAN_MIN) {
    pompaButuh = true;
    digitalWrite(RELAY_KIPAS, LOW);
    digitalWrite(RELAY_LAMPU, HIGH);
    statusLampu = "OFF";
    statusKipas = "ON";
    statusPompa = "ON";
  }

  // 4️⃣ KELEMBABAN RENDAH SUHU NORMAL
  else if (kelembaban <= SUHU_NORMAL_MIN && suhu >= SUHU_NORMAL_MIN && suhu <= SUHU_NORMAL_MAX) {
    pompaButuh = true;
    digitalWrite(RELAY_KIPAS, HIGH);
    digitalWrite(RELAY_LAMPU, HIGH);
    statusLampu = "OFF";
    statusKipas = "OFF";
    statusPompa = "ON";
  }

  // 4️⃣ KELEMBABAN TINGGI SUHU NORMAL
  else if (kelembaban >= SUHU_NORMAL_MAX && suhu >= SUHU_NORMAL_MIN && suhu <= SUHU_NORMAL_MAX) {
    digitalWrite(RELAY_POMPA, HIGH);
    digitalWrite(RELAY_KIPAS, LOW);
    digitalWrite(RELAY_LAMPU, HIGH);
    statusLampu = "OFF";
    statusKipas = "ON";
    statusPompa = "OFF";
  }

  // 4️⃣ KELEMBABAN TINGGI SUHU RENDAH
  else if (kelembaban >= SUHU_NORMAL_MAX && suhu <= SUHU_NORMAL_MIN) {
    digitalWrite(RELAY_POMPA, HIGH);
    digitalWrite(RELAY_KIPAS, HIGH);
    digitalWrite(RELAY_LAMPU, LOW);
    statusLampu = "ON";
    statusKipas = "OFF";
    statusPompa = "OFF";
  }

  // 5️⃣ Motor panen → TB6600 stepper
  if (jarak < JARAK_JAMUR) {
    stepper.moveTo(800);  // maju
    statusMotor = "ON";
  } else {
    stepper.moveTo(0);  // kembali
    statusMotor = "OFF";
  }
}

int bacaUltrasonik() {
  digitalWrite(TRIG_PIN, LOW);
  delayMicroseconds(2);
  digitalWrite(TRIG_PIN, HIGH);
  delayMicroseconds(10);
  digitalWrite(TRIG_PIN, LOW);
  long durasi = pulseIn(ECHO_PIN, HIGH, 20000);  // timeout 20ms
  int jarak = durasi * 0.034 / 2;
  return jarak;
}

void reconnectMQTT() {
  while (!client.connected()) {
    Serial.print("Menghubungkan ke MQTT...");

    if (client.connect(DEVICE_ULID, MQTT_USER, MQTT_PASSWORD)) {
      Serial.println("Terhubung!");

      char topic[100];
      sprintf(topic, "jamur/%s/control", DEVICE_ULID);
      client.setCallback(callback);
      client.subscribe(topic);

    } else {
      Serial.print("Gagal, rc=");
      Serial.print(client.state());
      Serial.println(" | coba lagi 5 detik...");
      delay(5000);
    }
  }
}

void callback(char* topic, byte* payload, unsigned int length) {
  String msg = "";
  for (int i = 0; i < length; i++) msg += (char)payload[i];

  StaticJsonDocument<200> doc;
  DeserializationError err = deserializeJson(doc, msg);
  if (err) return;

  if (doc.containsKey("lampu")) {
    lampuManualState = doc["lampu"].as<String>();
    manualLampu = (lampuManualState != "AUTO");
  }

  if (doc.containsKey("kipas")) {
    kipasManualState = doc["kipas"].as<String>();
    manualKipas = (kipasManualState != "AUTO");
  }

  if (doc.containsKey("pompa")) {
    pompaManualState = doc["pompa"].as<String>();
    manualPompaManual = (pompaManualState != "AUTO");
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
void updatePompa() {
  // Jika pompa perlu menyala dan sedang tidak aktif serta tidak dalam cooldown
  if (pompaButuh && !pompaAktif && (millis() - pompaCooldownStart >= cooldownPompa)) {
    digitalWrite(RELAY_POMPA, LOW);  // POMPA ON
    pompaAktif = true;
    pompaStartTime = millis();
    statusPompa = "ON";
  }

  // Jika pompa sudah ON lebih dari 3 detik → matikan & mulai cooldown
  if (pompaAktif && millis() - pompaStartTime >= durasiPompa) {
    digitalWrite(RELAY_POMPA, HIGH);  // POMPA OFF
    pompaAktif = false;
    pompaCooldownStart = millis();
    statusPompa = "OFF";
  }
}
