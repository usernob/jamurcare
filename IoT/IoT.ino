#include <WiFi.h>
#include "DHT.h"
#include <AccelStepper.h>
#include <PubSubClient.h>
#include <ArduinoJson.h>
#include "secrets.h"

// =====================================================
// PIN CONFIGURATION
// =====================================================
#define DHTPIN 26
#define DHTTYPE DHT22
#define TRIG_PIN 12
#define ECHO_PIN 13

#define RELAY_PUMP 17
#define RELAY_LAMP 14
#define RELAY_FAN 25

#define STEP_PIN 16
#define DIR_PIN 27
#define EN_PIN 5

// =====================================================
// THRESHOLD VALUES
// =====================================================
const float TEMP_MIN = 33.0;
const float TEMP_MAX = 34.0;
const float HUMIDITY_MIN = 86.0;
const float HUMIDITY_MAX = 87.0;
const int MUSHROOM_DISTANCE_CM = 10;

// =====================================================
// TIMING CONSTANTS
// =====================================================
const unsigned long SENSOR_READ_INTERVAL = 1000;   // 1 second
const unsigned long MQTT_PUBLISH_INTERVAL = 5000;  // 5 seconds
const unsigned long PUMP_ON_DURATION = 1500;       // 1.5 seconds
const unsigned long PUMP_COOLDOWN = 5000;          // 5 seconds

// =====================================================
// OBJECTS
// =====================================================
WiFiClient espClient;
PubSubClient client(espClient);
DHT dht(DHTPIN, DHTTYPE);
AccelStepper stepper(AccelStepper::DRIVER, STEP_PIN, DIR_PIN);

// =====================================================
// SENSOR DATA
// =====================================================
float temperature = 0.0;
float humidity = 0.0;
int distance = 0;

// =====================================================
// DEVICE STATUS
// =====================================================
String lampStatus = "OFF";
String pumpStatus = "OFF";
String fanStatus = "OFF";
String motorStatus = "OFF";

// =====================================================
// PUMP CONTROL STATE
// =====================================================
bool pumpNeeded = false;
bool pumpActive = false;
unsigned long pumpStartTime = 0;
unsigned long pumpCooldownStart = 0;

// =====================================================
// MANUAL CONTROL STATE
// =====================================================
bool manualLampControl = false;
bool manualPumpControl = false;
bool manualFanControl = false;

String lampManualState = "AUTO";
String pumpManualState = "AUTO";
String fanManualState = "AUTO";

// =====================================================
// TIMING VARIABLES
// =====================================================
unsigned long lastSensorRead = 0;
unsigned long lastMqttPublish = 0;

// =====================================================
// FLAGS
// =====================================================
bool needSendAck = false;

// =====================================================
// ENUMS & STRUCTS
// =====================================================
enum Level {
  LEVEL_LOW,
  LEVEL_NORMAL,
  LEVEL_HIGH
};

struct ControlRule {
  bool lamp;
  bool fan;
  bool pump;
};

// Control rules: [temperature level][humidity level]
ControlRule controlRules[3][3] = {
  // Temperature: LEVEL_LOW
  {
    { true, false, true },   // Humidity: LEVEL_LOW    -> Lamp ON, Pump ON
    { true, false, false },  // Humidity: LEVEL_NORMAL -> Lamp ON only
    { true, false, false }   // Humidity: LEVEL_HIGH   -> Lamp ON only
  },
  
  // Temperature: LEVEL_NORMAL
  {
    { false, false, true },  // Humidity: LEVEL_LOW    -> Pump ON only
    { false, false, false }, // Humidity: LEVEL_NORMAL -> All OFF
    { false, true, false }   // Humidity: LEVEL_HIGH   -> Fan ON only
  },
  
  // Temperature: LEVEL_HIGH
  {
    { false, true, true },   // Humidity: LEVEL_LOW    -> Fan ON, Pump ON
    { false, true, false },  // Humidity: LEVEL_NORMAL -> Fan ON only
    { false, true, false }   // Humidity: LEVEL_HIGH   -> Fan ON only
  }
};

// =====================================================
// FUNCTION DECLARATIONS
// =====================================================
void setupWiFi();
void setupMQTT();
void reconnectMQTT();
void mqttCallback(char* topic, byte* payload, unsigned int length);
void handleControlMessage(String message);
void sendStatusResponse();
void sendMonitoringData(float temp, float hum);

Level getTemperatureLevel(float temp);
Level getHumidityLevel(float hum);
int readUltrasonic();
void controlDevices(float temp, float hum, int dist);
void updatePumpCycle();
void turnOffAllDevices();

// =====================================================
// SETUP
// =====================================================
void setup() {
  Serial.begin(115200);
  Serial.println("\n=== Mushroom Farm Controller Starting ===");
  
  // Initialize DHT sensor
  dht.begin();
  
  // Configure pins
  pinMode(TRIG_PIN, OUTPUT);
  pinMode(ECHO_PIN, INPUT);
  pinMode(RELAY_PUMP, OUTPUT);
  pinMode(RELAY_LAMP, OUTPUT);
  pinMode(RELAY_FAN, OUTPUT);
  pinMode(EN_PIN, OUTPUT);
  
  // Enable TB6600 stepper driver
  digitalWrite(EN_PIN, LOW);
  
  // Configure stepper motor for cutting - high speed required
  stepper.setMaxSpeed(100000);      // Steps per second - adjust based on your motor
  stepper.setAcceleration(90000);  // Fast acceleration for cutting action
  
  // Turn off all devices initially
  turnOffAllDevices();
  
  // Connect to WiFi and MQTT
  setupWiFi();
  setupMQTT();
  
  Serial.println("=== Setup Complete ===\n");
}

// =====================================================
// MAIN LOOP
// =====================================================
void loop() {
  // Maintain MQTT connection
  if (!client.connected()) {
    reconnectMQTT();
  }
  client.loop();
  
  // Update pump timing cycle
  updatePumpCycle();
  
  // Run stepper motor
  stepper.run();
  
  unsigned long currentMillis = millis();
  
  // Read sensors periodically
  if (currentMillis - lastSensorRead >= SENSOR_READ_INTERVAL) {
    lastSensorRead = currentMillis;
    
    float newTemp = dht.readTemperature();
    float newHumidity = dht.readHumidity();
    int newDistance = readUltrasonic();
    
    if (!isnan(newTemp) && !isnan(newHumidity)) {
      temperature = newTemp;
      humidity = newHumidity;
      distance = newDistance;
      
      // Control devices based on sensor readings
      controlDevices(temperature, humidity, distance);
      
      // Print status to serial
      Serial.println("================================");
      Serial.printf("Temperature: %.1f °C\n", temperature);
      Serial.printf("Humidity: %.1f %%\n", humidity);
      Serial.printf("Distance: %d cm\n", distance);
      Serial.printf("Status -> Lamp:%s | Fan:%s | Pump:%s | Motor:%s\n", 
                    lampStatus.c_str(), fanStatus.c_str(), 
                    pumpStatus.c_str(), motorStatus.c_str());
      
      // Publish monitoring data periodically
      if (currentMillis - lastMqttPublish >= MQTT_PUBLISH_INTERVAL) {
        lastMqttPublish = currentMillis;
        sendMonitoringData(temperature, humidity);
      }
    } else {
      Serial.println("ERROR: Failed to read DHT22 sensor!");
    }
  }
}

// =====================================================
// NETWORK FUNCTIONS
// =====================================================
void setupWiFi() {
  Serial.println("Connecting to WiFi...");
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
  
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  
  Serial.println("\nWiFi Connected!");
  Serial.print("IP Address: ");
  Serial.println(WiFi.localIP());
}

void setupMQTT() {
  client.setServer(MQTT_SERVER, MQTT_PORT);
  client.setCallback(mqttCallback);
  reconnectMQTT();
}

void reconnectMQTT() {
  while (!client.connected()) {
    Serial.print("Connecting to MQTT broker...");
    
    if (client.connect(DEVICE_ULID, MQTT_USER, MQTT_PASSWORD)) {
      Serial.println("Connected!");
      
      // Subscribe to control and status request topics
      char controlTopic[100];
      char statusRequestTopic[100];
      sprintf(controlTopic, "jamur/%s/control", DEVICE_ULID);
      sprintf(statusRequestTopic, "jamur/%s/status_request", DEVICE_ULID);
      
      client.subscribe(controlTopic);
      client.subscribe(statusRequestTopic);
      
      Serial.printf("Subscribed to: %s\n", controlTopic);
      Serial.printf("Subscribed to: %s\n", statusRequestTopic);
    } else {
      Serial.printf("Failed! rc=%d. Retrying in 5 seconds...\n", client.state());
      delay(5000);
    }
  }
}

void mqttCallback(char* topic, byte* payload, unsigned int length) {
  String message = "";
  for (unsigned int i = 0; i < length; i++) {
    message += (char)payload[i];
  }
  
  String topicStr = String(topic);
  Serial.printf("MQTT Message received on topic: %s\n", topic);
  Serial.printf("Payload: %s\n", message.c_str());
  
  if (topicStr.endsWith("/control")) {
    handleControlMessage(message);
  } else if (topicStr.endsWith("/status_request")) {
    sendStatusResponse();
  }
}

void handleControlMessage(String message) {
  StaticJsonDocument<200> doc;
  DeserializationError error = deserializeJson(doc, message);
  
  if (error) {
    Serial.printf("JSON parsing failed: %s\n", error.c_str());
    return;
  }
  
  // Handle lamp control
  if (doc.containsKey("lamp")) {
    lampManualState = doc["lamp"].as<String>();
    manualLampControl = (lampManualState != "AUTO");
  }
  
  // Handle fan control
  if (doc.containsKey("fan")) {
    fanManualState = doc["fan"].as<String>();
    manualFanControl = (fanManualState != "AUTO");
  }
  
  // Handle pump control
  if (doc.containsKey("pump")) {
    pumpManualState = doc["pump"].as<String>();
    manualPumpControl = (pumpManualState != "AUTO");
  }
  
  needSendAck = true;
}

void sendStatusResponse() {
  StaticJsonDocument<200> doc;
  doc["device_ulid"] = DEVICE_ULID;
  doc["message"] = "pong";
  doc["lamp"] = manualLampControl ? lampStatus : "AUTO";
  doc["fan"] = manualFanControl ? fanStatus : "AUTO";
  doc["pump"] = manualPumpControl ? pumpStatus : "AUTO";
  
  char buffer[200];
  size_t length = serializeJson(doc, buffer);
  
  char topic[100];
  sprintf(topic, "jamur/%s/status_response", DEVICE_ULID);
  client.publish(topic, buffer, length);
  
  Serial.println("Status response sent");
}

void sendMonitoringData(float temp, float hum) {
  StaticJsonDocument<200> doc;
  doc["device_ulid"] = DEVICE_ULID;
  doc["temperature"] = temp;
  doc["humidity"] = hum;
  
  char buffer[200];
  size_t length = serializeJson(doc, buffer);
  
  char topic[100];
  sprintf(topic, "jamur/%s/monitoring", DEVICE_ULID);
  
  if (client.publish(topic, buffer, length)) {
    Serial.println("Monitoring data published");
  }
}

// =====================================================
// SENSOR FUNCTIONS
// =====================================================
int readUltrasonic() {
  digitalWrite(TRIG_PIN, LOW);
  delayMicroseconds(2);
  digitalWrite(TRIG_PIN, HIGH);
  delayMicroseconds(10);
  digitalWrite(TRIG_PIN, LOW);
  
  long duration = pulseIn(ECHO_PIN, HIGH, 20000);  // 20ms timeout
  int distance = duration * 0.034 / 2;
  
  return distance;
}

Level getTemperatureLevel(float temp) {
  if (temp < TEMP_MIN) return LEVEL_LOW;
  if (temp > TEMP_MAX) return LEVEL_HIGH;
  return LEVEL_NORMAL;
}

Level getHumidityLevel(float hum) {
  if (hum < HUMIDITY_MIN) return LEVEL_LOW;
  if (hum > HUMIDITY_MAX) return LEVEL_HIGH;
  return LEVEL_NORMAL;
}

// =====================================================
// CONTROL LOGIC
// =====================================================
void controlDevices(float temp, float hum, int dist) {
  Level tempLevel = getTemperatureLevel(temp);
  Level humLevel = getHumidityLevel(hum);
  
  // Get control rule based on current levels
  ControlRule rule = controlRules[tempLevel][humLevel];
  
  bool lampOn = rule.lamp;
  bool fanOn = rule.fan;
  bool pumpOn = rule.pump;
  
  // Apply manual overrides if active
  if (manualLampControl) {
    lampOn = (lampManualState == "ON");
  }
  
  if (manualFanControl) {
    fanOn = (fanManualState == "ON");
  }
  
  if (manualPumpControl) {
    pumpOn = (pumpManualState == "ON");
  }
  
  // Control relays (active LOW)
  digitalWrite(RELAY_LAMP, lampOn ? LOW : HIGH);
  digitalWrite(RELAY_FAN, fanOn ? LOW : HIGH);
  
  // Update pump state (actual control in updatePumpCycle)
  pumpNeeded = pumpOn;
  
  // Update status strings
  lampStatus = lampOn ? "ON" : "OFF";
  fanStatus = fanOn ? "ON" : "OFF";
  
  // Control stepper motor for cutting based on distance
  if (dist < MUSHROOM_DISTANCE_CM && dist > 0) {
    // Move forward quickly for cutting
    if (stepper.currentPosition() == 0) {
      stepper.moveTo(800);  // Adjust steps based on your cutting blade travel distance
    }
    motorStatus = "ON";
  } else {
    // Return to home position
    if (stepper.currentPosition() != 0) {
      stepper.moveTo(0);
    }
    motorStatus = "OFF";
  }
  
  // Send acknowledgment if needed
  if (needSendAck) {
    sendStatusResponse();
    needSendAck = false;
  }
}

void updatePumpCycle() {
  unsigned long currentMillis = millis();
  
  // Start pump if needed and not in cooldown
  if (pumpNeeded && !pumpActive) {
    // Check if cooldown period has elapsed
    if (currentMillis - pumpCooldownStart >= PUMP_COOLDOWN) {
      digitalWrite(RELAY_PUMP, LOW);  // Turn ON (active LOW)
      pumpActive = true;
      pumpStartTime = currentMillis;
      pumpStatus = "ON";
      Serial.println("Pump: ON");
    } else {
      // In cooldown, show OFF status
      pumpStatus = "OFF";
    }
  }
  
  // Turn off pump after ON duration
  if (pumpActive && (currentMillis - pumpStartTime >= PUMP_ON_DURATION)) {
    digitalWrite(RELAY_PUMP, HIGH);  // Turn OFF
    pumpActive = false;
    pumpCooldownStart = currentMillis;
    pumpStatus = "OFF";
    Serial.println("Pump: OFF (cooldown started)");
  }
}

void turnOffAllDevices() {
  digitalWrite(RELAY_PUMP, HIGH);
  digitalWrite(RELAY_LAMP, HIGH);
  digitalWrite(RELAY_FAN, HIGH);
  
  lampStatus = "OFF";
  pumpStatus = "OFF";
  fanStatus = "OFF";
  motorStatus = "OFF";
  pumpNeeded = false;
  pumpActive = false;
}