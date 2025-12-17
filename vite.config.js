import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
	plugins: [
		laravel({
			input: [
				"resources/css/app.css",
				"resources/js/app.js",
				"resources/js/auth-common.js",
				"resources/js/dashboard/index.js",
			],
			refresh: true,
		}),
		tailwindcss(),
	],
});
