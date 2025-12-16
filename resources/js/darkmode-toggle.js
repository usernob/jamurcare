// On page load or when changing themes, best to add inline in `head` to avoid FOUC
let isDarkMode =
	localStorage.theme === "dark" ||
	(!("theme" in localStorage) &&
		window.matchMedia("(prefers-color-scheme: dark)").matches);

const moon_icon = document.querySelector("#icon-dark");
const sun_icon = document.querySelector("#icon-light");

if (moon_icon != undefined || sun_icon != undefined) {
	if (isDarkMode) {
		moon_icon.classList.toggle("!hidden");
	} else {
		sun_icon.classList.toggle("!hidden");
	}
}

document.documentElement.dataset.theme = isDarkMode ? "dark" : "light";

document.querySelector("#darkmode-toggler").addEventListener("click", (_) => {
	isDarkMode = !isDarkMode;
	localStorage.theme = isDarkMode ? "dark" : "light";
	document.documentElement.dataset.theme = isDarkMode ? "dark" : "light";

	if (moon_icon != undefined || sun_icon != undefined) {
		moon_icon.classList.toggle("!hidden");
		sun_icon.classList.toggle("!hidden");
	}
});
