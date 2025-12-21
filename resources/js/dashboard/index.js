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

(async function () {
	const monitoring_chart = new MonitoringChart();
	Echo.private(`device.${window.monitoring.device_ulid}`)
		.listen("DeviceMonitoringUpdate", (e) => {
			monitoring_chart.updateMonitoringChart(e);
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

	axios.get(`/api/ping/${window.monitoring.device_ulid}`);

	initDropdown();

	createStateCallback("#pump-state");
	createStateCallback("#lamp-state");
	createStateCallback("#fan-state");

	resetTimeout();
})();
