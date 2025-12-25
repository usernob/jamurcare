// On page load or when changing themes, best to add inline in `head` to avoid FOUC
(function () {
    let isDarkMode =
        localStorage.theme === "dark" ||
        (!("theme" in localStorage) &&
            window.matchMedia("(prefers-color-scheme: dark)").matches);

    function updateAllTogglers() {
        const allTogglers = document.querySelectorAll("#darkmode-toggler");

        allTogglers.forEach((toggler) => {
            const moonIcon = toggler.querySelector("#icon-dark, .icon-dark");
            const sunIcon = toggler.querySelector("#icon-light, .icon-light");

            if (moonIcon && sunIcon) {
                if (isDarkMode) {
                    moonIcon.classList.remove("!hidden");
                    sunIcon.classList.add("!hidden");
                } else {
                    moonIcon.classList.add("!hidden");
                    sunIcon.classList.remove("!hidden");
                }
            }
        });

        document.documentElement.dataset.theme = isDarkMode ? "dark" : "light";
    }

    function toggleDarkMode() {
        isDarkMode = !isDarkMode;
        localStorage.theme = isDarkMode ? "dark" : "light";
        updateAllTogglers();
    }

    updateAllTogglers();

    document.addEventListener("DOMContentLoaded", () => {
        const allTogglers = document.querySelectorAll("#darkmode-toggler");

        allTogglers.forEach((toggler) => {
            toggler.addEventListener("click", toggleDarkMode);
        });
    });

    window.addEventListener("storage", (e) => {
        if (e.key === "theme") {
            isDarkMode = e.newValue === "dark";
            updateAllTogglers();
        }
    });
})();
