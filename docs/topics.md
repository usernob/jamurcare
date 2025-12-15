## 1. jamur/{device_ulid}/monitoring

Event: interval (misal setiap 5–10 detik)  
Flow: IoT → Server  
Payload:  

```json
{
  "device_ulid": "string",
  "temperature": "float",
  "humidity": "float"
}
```

## 2. jamur/{device_ulid}/control  

Event: saat server ingin mengatur fan/pump/lamp  
Flow: Server → IoT  
Payload:  

```json
{
  "fan": "ON | OFF | AUTO",
  "pump": "ON | OFF | AUTO",
  "lamp": "ON | OFF | AUTO"
}
```


## 3. jamur/{device_ulid}/status_request  

Event: Server meminta status IoT, biasanya saat halaman monitoring dibuka  
Flow: Server → IoT  
Payload:  

```json
{
  "message": "ping"
}
```

## 4. jamur/{device_ulid}/status_response  

Event: IoT membalas permintaan status dan control  
Flow: IoT → Server  
Payload:  

```json
{
  "device_ulid": "string",
  "message": "pong",
  "fan": "ON | OFF | AUTO",
  "pump": "ON | OFF | AUTO",
  "lamp": "ON | OFF | AUTO"
}
```
