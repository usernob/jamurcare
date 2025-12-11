"use strict";

import axios from "axios";
import {
	Chart,
	LineController,
	LineElement,
	PointElement,
	LinearScale,
	TimeScale,
	Tooltip,
	Filler,
} from "chart.js";
import "chartjs-adapter-date-fns";

class RingBuffer {
	constructor(size, initialData = []) {
		this.size = size;
		this.buffer = new Array(size).fill(null);
		this.index = 0;
		this.isFull = false;

		if (initialData.length > 0) {
			this.pushBatch(initialData);
		}
	}

	push(value) {
		this.buffer[this.index] = value;
		this.index = (this.index + 1) % this.size;

		if (this.index === 0) {
			this.isFull = true;
		}
	}

	top() {
		return this.buffer[this.index - 1];
	}

	pushBatch(values) {
		for (const v of values) {
			this.push(v);
		}
	}

	getData() {
		if (!this.isFull) {
			return this.buffer.slice(0, this.index);
		}

		return this.buffer
			.slice(this.index)
			.concat(this.buffer.slice(0, this.index));
	}
}

function setAlpha(rgba, alpha) {
	const parts = rgba.trim().slice(5, -1).split(",");
	parts[3] = alpha;
	return `rgba(${parts.join(",")})`;
}

const verticalLinePlugin = {
	id: "verticalLine",
	afterDraw(chart, args, options) {
		const { ctx, tooltip, chartArea } = chart;

		if (!tooltip?.getActiveElements().length) {
			return;
		}

		const x = tooltip.caretX;

		ctx.save();
		ctx.beginPath();
		ctx.moveTo(x, chartArea.top);
		ctx.lineTo(x, chartArea.bottom);
		ctx.lineWidth = options.width || 1;
		ctx.strokeStyle = options.color || "rgba(0,0,0,0.2)";
		ctx.stroke();
		ctx.restore();
	},
};

Chart.register(
	verticalLinePlugin,
	LineController,
	LineElement,
	PointElement,
	LinearScale,
	TimeScale,
	Tooltip,
	Filler,
);

const styles = getComputedStyle(document.documentElement);
const primaryColor = styles.getPropertyValue("--color-primary").trim();
const primaryTransparentColor = setAlpha(primaryColor, 0);

function createChart(elementId, label, data) {
	const canvas = document.getElementById(elementId);
	const ctx = canvas.getContext("2d");

	const gradient = ctx.createLinearGradient(0, -100, 0, 300);
	gradient.addColorStop(0, primaryColor);
	gradient.addColorStop(1, primaryTransparentColor);
	const chart = new Chart(ctx, {
		type: "line",
		data: {
			label: label,
			datasets: [
				{
					label: label,
					data: data,

					borderColor: primaryColor,
					backgroundColor: gradient,
					borderWidth: 2,
					fill: true,
					pointRadius: 0,
					pointHoverRadius: 0,
					tension: 0,
				},
			],
		},
		options: {
			responsive: true,
			plugins: {
				legend: { display: false },
				verticalLine: {
					color: primaryColor,
					width: 0.6,
				},
				tooltip: {
					mode: "nearest",
					intersect: false,
				},
			},
			interaction: {
				mode: "nearest",
				intersect: false,
			},
			parsing: false,
			scales: {
				x: {
					type: "time",
					time: {
						unit: "minute",
					},
					grid: { display: false },
				},
				y: {
					beginAtZero: false,
					grid: { display: false },
				},
			},
		},
	});
	return chart;
}

function updateChart(chart, data) {
	chart.data.datasets[0].data = data;
	chart.update("none");
}

function easeOutQuad(t) {
	return t * (2 - t);
}

function animateValue(start, end, duration, onUpdate) {
	const startTime = performance.now();

	function tick(now) {
		let progress = Math.min((now - startTime) / duration, 1);
		progress = easeOutQuad(progress);

		const value = start + (end - start) * progress;
		onUpdate(value);

		if (progress < 1) requestAnimationFrame(tick);
	}

	requestAnimationFrame(tick);
}

let current_temperature = 0;
const temperature_label = document.getElementById("temperature-label");
function onNewTemperature(newValue) {
	animateValue(current_temperature, newValue, 300, (v) => {
		current_temperature = v;
		temperature_label.textContent = v.toFixed(1);
	});
}

let current_humidity = 0;
const humidity_label = document.getElementById("humidity-label");
function onNewHumidity(newValue) {
	animateValue(current_humidity, newValue, 300, (v) => {
		current_humidity = v;
		humidity_label.textContent = v.toFixed(1);
	});
}

(async function () {
	try {
		let res = await axios.get(
			`/api/monitoring/${window.monitoring.device_ulid}`,
		);

		let temp_rb = new RingBuffer(120, res.data.temperature.reverse()); // reverse because the data is descending
		let hum_rb = new RingBuffer(120, res.data.humidity.reverse());
		const temperature_chart = createChart(
			"temperature-chart",
			"Temperature",
			temp_rb.getData(),
		);
		const humidity_chart = createChart(
			"humidity-chart",
			"Humidity",
			hum_rb.getData(),
		);

		current_temperature = temp_rb.top().y;
		current_humidity = hum_rb.top().y;
		temperature_label.textContent = current_temperature.toFixed(1);
		humidity_label.textContent = current_humidity.toFixed(1);

		Echo.channel(`monitoring.${window.monitoring.device_ulid}`).listen(
			"DeviceMonitoringUpdate",
			(e) => {
				temp_rb.push(e.temperature);
				hum_rb.push(e.humidity);

				updateChart(temperature_chart, temp_rb.getData());
				onNewTemperature(e.temperature.y);
				updateChart(humidity_chart, hum_rb.getData());
				onNewHumidity(e.humidity.y);
			},
		);
	} catch (err) {
		console.log(err);
	}
})();
