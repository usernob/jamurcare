import mqtt from "mqtt";
import { ulid } from "ulid";
import dotenv from "dotenv";
import path from "path";
import { fileURLToPath } from "url";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

dotenv.config({
	path: path.resolve(__dirname, "..", ".env"), // naik ke parent folder
});

console.log(process.env.MQTT_HOST);
console.log(process.env.MQTT_PORT);
console.log(process.env.MQTT_AUTH_USERNAME);
console.log(process.env.MQTT_AUTH_PASSWORD);

const DEVICE_UID = ulid(); // atau hardcode kalau mau
const TOPIC = `jamur/${DEVICE_UID}/monitoring`;
const MQTT_HOST = process.env.MQTT_HOST;
const MQTT_PORT = process.env.MQTT_PORT;
const MQTT_AUTH_USERNAME = process.env.MQTT_AUTH_USERNAME;
const MQTT_AUTH_PASSWORD = process.env.MQTT_AUTH_PASSWORD;
const client = mqtt.connect(`mqtt://${MQTT_HOST}:${MQTT_PORT}`, {
	username: MQTT_AUTH_USERNAME,
	password: MQTT_AUTH_PASSWORD,
});

client.on("connect", () => {
	console.log("Connected as IoT device");
	console.log("Device ULID:", DEVICE_UID);
	console.log("Publishing to:", TOPIC);

	let temp = 28;
	let hum = 60;
	setInterval(() => {
		temp += Math.random() * 6 - 3;
		hum += Math.random() * 6 - 3;
		const payload = {
			device_uid: DEVICE_UID,
			temperature: temp.toFixed(2),
			humidity: hum.toFixed(2),
			recorded_at: new Date().toISOString(),
		};

		client.publish(TOPIC, JSON.stringify(payload), {
			qos: 1,
		});
		console.log("Sent:", payload);
	}, 60000);
});

client.on("error", (err) => {
	console.error("MQTT Error:", err);
});
