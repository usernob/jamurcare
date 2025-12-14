import mqtt from "mqtt";
import dotenv from "dotenv";
import path from "path";
import { fileURLToPath } from "url";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

dotenv.config({
	path: path.resolve(__dirname, "..", ".env"),
});

const DEVICE_UID = "01kc4kqwvwwt6xc7nrhq595q79";
const MQTT_HOST = process.env.MQTT_HOST;
const MQTT_PORT = process.env.MQTT_PORT;
const MQTT_AUTH_USERNAME = process.env.MQTT_AUTH_USERNAME;
const MQTT_AUTH_PASSWORD = process.env.MQTT_AUTH_PASSWORD;

const client = mqtt.connect(`mqtt://${MQTT_HOST}:${MQTT_PORT}`, {
	username: MQTT_AUTH_USERNAME,
	password: MQTT_AUTH_PASSWORD,
	clientId: `iot-${DEVICE_UID}`,
});

let deviceState = {
	fan: "AUTO",
	pump: "AUTO",
	lamp: "OFF",
};

client.on("connect", () => {
	console.log("Connected as IoT device:", DEVICE_UID);

	client.subscribe(`jamur/${DEVICE_UID}/control`, { qos: 1 });
	client.subscribe(`jamur/${DEVICE_UID}/status_request`, { qos: 1 });

	let temp = 33;
	let hum = 65;

	setInterval(() => {
		temp += Math.random() * 0.2 - 0.1;
		hum += Math.random() * 0.2 - 0.1;

		const payload = {
			device_ulid: DEVICE_UID,
			temperature: temp.toFixed(2),
			humidity: hum.toFixed(2),
		};

		const topic = `jamur/${DEVICE_UID}/monitoring`;
		client.publish(topic, JSON.stringify(payload), { qos: 1 });

		console.log("Monitoring sent:", payload);
	}, 5000);
});

client.on("message", (topic, message) => {
	let payload;
	try {
		payload = JSON.parse(message.toString());
	} catch {
		return;
	}

	if (topic === `jamur/${DEVICE_UID}/control`) {
		console.log("Control received:", payload);

		if (payload.fan) deviceState.fan = payload.fan;
		if (payload.pump) deviceState.pump = payload.pump;
		if (payload.lamp) deviceState.lamp = payload.lamp;

		publishStatus("pong");
	}

	if (topic === `jamur/${DEVICE_UID}/status_request`) {
		console.log("Status request:", payload);

		if (payload.message === "ping") {
			publishStatus("pong");
		}
	}
});

function publishStatus(message) {
	const response = {
		device_ulid: DEVICE_UID,
		message,
		fan: deviceState.fan,
		pump: deviceState.pump,
		lamp: deviceState.lamp,
	};

	const topic = `jamur/${DEVICE_UID}/status_response`;
	client.publish(topic, JSON.stringify(response), { qos: 1 });

	console.log("Status response sent:", response);
}

client.on("error", (err) => {
	console.error("MQTT Error:", err);
});
