## 1. jamur/{device_ulid}/monitoring

Event: interval (misal setiap 5–10 detik)
Publish: IoT → Server
Subscribe: Server
Payload:

{
  "device_ulid": "string",
  "temperature": "float",
  "humidity": "float"
}

## 2. jamur/{device_ulid}/control

Event: saat server ingin mengatur fan/pump/lamp
Publish: Server → IoT
Subscribe: IoT
Payload:

{
  "fan": "ON | OFF | AUTO",
  "pump": "ON | OFF | AUTO",
  "lamp": "ON | OFF | AUTO"
}


## 3. jamur/{device_ulid}/status_request

Event: Server meminta status IoT, biasanya saat halaman monitoring dibuka
Publish: Server → IoT
Subscribe: IoT
Payload:

{
  "message": "ping"
}

## 4. jamur/{device_ulid}/status_response

Event: IoT membalas permintaan status dan control
Publish: IoT → Server
Subscribe: Server
Payload:

{
  "device_ulid": "string",
  "message": "pong",
  "fan": "ON | OFF | AUTO",
  "pump": "ON | OFF | AUTO",
  "lamp": "ON | OFF | AUTO"
}
