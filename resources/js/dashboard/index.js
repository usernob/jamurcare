"use strict";

import MonitoringChart from "./charting";
import { initDropdown } from "./dropdown";

const State = {
	fan: "OFF",
	lamp: "OFF",
	pump: "OFF",
};

let deviceTimer;
const TIMEOUT = 10000; // 10 seconds
const status_label = document.getElementById("device-status");

function sendControl(state) {
	axios.post(`/api/control/${window.monitoring.device_ulid}`, state);
}

function setStateLabel(id, loading = true) {
	const load = document.querySelector(`#${id}-state .state-load`);
	const label = document.querySelector(`#${id}-state .state-label`);

	load.classList.toggle("hidden", !loading);
	label.classList.toggle("hidden", loading);

	if (!loading) {
		label.textContent = State[id];
	}
}

function createStateCallback(element_query) {
	const element = document.querySelector(element_query);
	const state_menus = element.querySelector("[data-dropdown-menu]").children;
	const state_name = element.dataset["state"];

	setStateLabel(state_name, true);

	for (let i = 0; i < state_menus.length; i++) {
		const child = state_menus[i];

		child.addEventListener("click", (e) => {
			const new_state = e.currentTarget.textContent;
			const temp_state = State;
			temp_state[state_name] = new_state;
			setStateLabel(state_name, true);
			sendControl(temp_state);
		});
	}
}

function resetTimeout() {
	if (deviceTimer) {
		status_label.textContent = "Online";
		status_label.dataset["status"] = "online";
		clearTimeout(deviceTimer);
	}

	deviceTimer = setTimeout(() => {
		status_label.textContent = "Offline";
		status_label.dataset["status"] = "offline";
	}, TIMEOUT);
}

function updateAIInsight(temp, hum) {
	if (isNaN(temp) || isNaN(hum)) {
		document.getElementById("ai-insight").textContent =
			"Menunggu data dari sistem IoT...";
		return;
	}

	let insight = "";
	if (temp > 32 && hum < 45) {
		insight =
			"Suhu sangat tinggi & kelembapan rendah. Sistem telah meningkatkan irigasi.";
	} else if (temp < 18 && hum > 85) {
		insight = "Suhu rendah & kelembapan tinggi. Risiko jamur meningkat.";
	} else if (temp >= 22 && temp <= 28 && hum >= 60 && hum <= 75) {
		insight = "Kondisi optimal! Tanaman berada dalam zona nyaman.";
	} else if (temp > 28 && hum > 80) {
		insight = "Suhu & kelembapan tinggi. Kipas otomatis diaktifkan.";
	} else {
		insight = "Kondisi stabil. Sistem terus memantau untuk optimasi real-time.";
	}

	document.getElementById("ai-insight").textContent = insight;
}

function formatDate(dateStr) {
	const d = new Date(dateStr);
	return d.toLocaleDateString("id-ID", {
		weekday: "short",
		day: "2-digit",
		month: "short",
	});
}

function isToday(dateStr) {
	const d = new Date(dateStr);
	const now = new Date();
	return (
		d.getDate() === now.getDate() &&
		d.getMonth() === now.getMonth() &&
		d.getFullYear() === now.getFullYear()
	);
}
(async function () {
	const monitoring_chart = new MonitoringChart();
	Echo.private(`device.${window.monitoring.device_ulid}`)
		.listen("DeviceMonitoringUpdate", (e) => {
			monitoring_chart.updateMonitoringChart(e);
			updateAIInsight(e.temperature.y, e.humidity.y);
			resetTimeout();
		})
		.listen("DeviceStatusUpdate", (e) => {
			State.pump = e.pump;
			State.lamp = e.lamp;
			State.fan = e.fan;

			setStateLabel("pump", false);
			setStateLabel("lamp", false);
			setStateLabel("fan", false);

			resetTimeout();
		});

	axios.get(`/api/monitoring/${window.monitoring.device_ulid}`).then((res) => {
		const container = document.getElementById("weekly-summary");
		const template = document.getElementById("summary-card-template");

		container.innerHTML = "";

		res.data.recap.reverse().forEach((item) => {
			const clone = template.content.cloneNode(true);
			const card = clone.firstElementChild;

			// tanggal
			card.querySelector(".date").textContent = formatDate(item.period_start);

			// temperature & humidity
			card.querySelector(".temperature").textContent =
				Number(item.avg_temperature).toFixed(1) + "°";
			card.querySelector(".humidity").textContent =
				Number(item.avg_humidity).toFixed(0) + "%";

			// highlight hari ini
			if (isToday(item.period_start)) {
				card.classList.remove("border-outline/30");
				card.classList.add("border-primary");

				const badge = card.querySelector(".badge");
				badge.classList.remove("hidden");

				card.querySelector(".date").classList.add("text-primary");
			}

			container.appendChild(clone);
		});

		monitoring_chart.init(
			res.data.temperature.reverse(),
			res.data.humidity.reverse(),
		);
	});

	axios.get(`/api/ping/${window.monitoring.device_ulid}`);

	initDropdown();

	createStateCallback("#pump-state");
	createStateCallback("#lamp-state");
	createStateCallback("#fan-state");

	resetTimeout();
})();
