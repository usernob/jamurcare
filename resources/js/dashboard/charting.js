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
		return this.buffer[this.index];
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

class MonitoringChart {
	constructor() {
		this.styles = getComputedStyle(document.documentElement);
		this.primaryColor = this.styles.getPropertyValue("--color-primary").trim();
		this.primaryTransparentColor = this.setAlpha(this.primaryColor, 0);

		this.current_temperature = 0;
		this.temperature_label = document.getElementById("temperature-label");

		this.current_humidity = 0;
		this.humidity_label = document.getElementById("humidity-label");

		Chart.register(
			this.verticalLinePlugin(),
			LineController,
			LineElement,
			PointElement,
			LinearScale,
			TimeScale,
			Tooltip,
			Filler,
		);

		this.temp_rb = new RingBuffer(720);
		this.hum_rb = new RingBuffer(720);
		this.temperature_chart = this.createChart(
			"temperature-chart",
			"Temperature",
			this.temp_rb.getData(),
		);
		this.humidity_chart = this.createChart(
			"humidity-chart",
			"Humidity",
			this.hum_rb.getData(),
		);
	}

	init(temperature, humidity) {
		this.temp_rb.pushBatch(temperature);
		this.hum_rb.pushBatch(humidity);
		this.updateChart(this.temperature_chart, this.temp_rb.getData());
		this.updateChart(this.humidity_chart, this.hum_rb.getData());

		this.current_temperature = this.temp_rb.top().y;
		this.current_humidity = this.hum_rb.top().y;
		this.temperature_label.textContent = this.current_temperature.toFixed(1);
		this.humidity_label.textContent = this.current_humidity.toFixed(1);
	}

	setAlpha(color, alpha) {
		color = color.trim();

		if (color.startsWith("#")) {
			let hex = color.slice(1);

			if (hex.length === 3) {
				hex = hex
					.split("")
					.map((c) => c + c)
					.join("");
			}

			const r = parseInt(hex.slice(0, 2), 16);
			const g = parseInt(hex.slice(2, 4), 16);
			const b = parseInt(hex.slice(4, 6), 16);

			return `rgba(${r}, ${g}, ${b}, ${alpha})`;
		}

		if (color.startsWith("rgb")) {
			const nums = color.match(/\d+(\.\d+)?/g);
			if (!nums || nums.length < 3) return color;

			return `rgba(${nums[0]}, ${nums[1]}, ${nums[2]}, ${alpha})`;
		}

		return color;
	}

	updateChart(chart, data) {
		chart.data.datasets[0].data = data;
		chart.update("none");
	}

	animateValue(start, end, duration, onUpdate) {
		const startTime = performance.now();

		function tick(now) {
			let progress = Math.min((now - startTime) / duration, 1);
			progress = progress * (2 - progress);

			const value = start + (end - start) * progress;
			onUpdate(value);

			if (progress < 1) requestAnimationFrame(tick);
		}

		requestAnimationFrame(tick);
	}

	verticalLinePlugin() {
		return {
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
	}

	createChart(elementId, label, data) {
		const canvas = document.getElementById(elementId);
		const ctx = canvas.getContext("2d");

		const gradient = ctx.createLinearGradient(0, -100, 0, 300);

		gradient.addColorStop(0, this.primaryColor);
		gradient.addColorStop(1, this.primaryTransparentColor);
		const chart = new Chart(ctx, {
			type: "line",
			data: {
				label: label,
				datasets: [
					{
						label: label,
						data: data,

						borderColor: this.primaryColor,
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
						color: this.primaryColor,
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
						ticks: {
							autoSkip: true,
							maxRotation: 0,
							maxTicksLimit: 6,
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

	updateMonitoringChart(data) {
		this.temp_rb.push(data.temperature);
		this.hum_rb.push(data.humidity);
		this.updateChart(this.temperature_chart, this.temp_rb.getData());
		this.animateValue(
			this.current_temperature,
			data.temperature.y,
			300,
			(v) => {
				this.current_temperature = v;
				this.temperature_label.textContent = v.toFixed(1);
			},
		);

		this.updateChart(this.humidity_chart, this.hum_rb.getData());
		this.animateValue(this.current_humidity, data.humidity.y, 300, (v) => {
			this.current_humidity = v;
			this.humidity_label.textContent = v.toFixed(1);
		});
	}
}

export default MonitoringChart;
