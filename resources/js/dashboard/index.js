"use strict";

import MonitoringChart from "./charting";

function createDropdown(btn_id, menu_id) {
	const btn = document.getElementById(btn_id);
	const menu = document.getElementById(menu_id);

	if (btn == undefined) return;

	btn.addEventListener("click", () => {
		menu.classList.toggle("hidden");
	});
}

(async function () {
	const monitoring_chart = new MonitoringChart();
	Echo.channel(`monitoring.${window.monitoring.device_ulid}`).listen(
		"DeviceMonitoringUpdate",
		(e) => {
			monitoring_chart.updateMonitoringChart(e);
		},
	);

	createDropdown("devicelist", "devicelist-menu");
})();
