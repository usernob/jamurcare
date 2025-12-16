"use strict";

import MonitoringChart from "./charting";

const State = {
	fan: "OFF",
	lamp: "OFF",
	pump: "OFF",
};

let deviceTimer;
const TIMEOUT = 10000; // 10 seconds
const status_label = document.getElementById("device-status");

function sendControl(state) {
	axios
		.post(`/api/control/${window.monitoring.device_ulid}`, state)
		.then((res) => {
			console.log("Control sent:", res.data);
		})
		.catch((err) => {
			console.error("Failed send control:", err);
		});
}

function createDropdown(btn, btn_callback) {
	btn.addEventListener("click", (e) => {
		e.currentTarget.parentElement
			.querySelector(`#${e.currentTarget.dataset["targetid"]}`)
			.classList.toggle("hidden");

		btn_callback(e);
	});
}

function createStateCallback(element_query) {
	const element = document.querySelector(element_query);

	const dropdown = element.querySelector(".state-dropdown");
	const label = element.querySelector(".state-label");

	createDropdown(dropdown, (e) => {});

	const state_menus = element.querySelector(".state-menu").children;
	for (let i = 0; i < state_menus.length; i++) {
		const child = state_menus[i];

		child.addEventListener("click", (e) => {
			const new_state = e.currentTarget.textContent;
			const state_name = element.dataset["state"];
			State[state_name] = new_state;
            label.textContent = new_state;
            sendControl(State);
		});
	}
}

function resetTimeout() {
	if (deviceTimer) {
		clearTimeout(deviceTimer);
	}

	deviceTimer = setTimeout(() => {
		status_label.textContent = "Offline";
        status_label.dataset["status"] = "offline";
	}, TIMEOUT);
}

(async function () {
	const monitoring_chart = new MonitoringChart();
	Echo.channel(window.monitoring.device_ulid)
		.listen("DeviceMonitoringUpdate", (e) => {
			monitoring_chart.updateMonitoringChart(e);
			status_label.textContent = "Online";
            status_label.dataset["status"] = "online";
			resetTimeout();
		})
		.listen("DeviceStatusUpdate", (e) => {
            State.lamp = e.lamp;
            State.pump = e.pump;
            State.fan = e.fan;
            document.querySelector("#lamp-state .state-label").textContent = State.lamp;
            document.querySelector("#pump-state .state-label").textContent = State.pump;
            document.querySelector("#fan-state .state-label").textContent = State.fan;
		});

	const btn = document.querySelector("#dropdown-device");
	createDropdown(btn, (e) => {
		e.currentTarget.querySelector(".arrow-drop").classList.toggle("rotate-180");
	});

    axios.get(`/api/ping/${window.monitoring.device_ulid}`)

	createStateCallback("#pump-state");
	createStateCallback("#lamp-state");
	createStateCallback("#fan-state");

	resetTimeout();
})();
