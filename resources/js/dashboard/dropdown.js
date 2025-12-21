const dropdownRegistry = new Set();

export function initDropdownRaw(dropdown, onToggle = () => {}) {
	const trigger = dropdown.querySelector("[data-dropdown-trigger]");
	const menu = dropdown.querySelector("[data-dropdown-menu]");

	const dropdownData = {
		trigger,
		menu,
		open: false,
		openMenu() {
			menu.style.maxHeight = menu.scrollHeight + "px";
			menu.style.opacity = "1";
			menu.style.pointerEvents = "auto";
			this.open = true;
			onToggle(trigger, menu, true);
		},
		closeMenu() {
			menu.style.maxHeight = "0px";
			menu.style.opacity = "0";
			menu.style.pointerEvents = "none";

			this.open = false;
			onToggle(trigger, menu, false);
		},
	};

	trigger.addEventListener("click", (e) => {
		e.stopPropagation();
		dropdownData.open ? dropdownData.closeMenu() : dropdownData.openMenu();
	});

	dropdownRegistry.add(dropdownData);
}

export function initDropdown() {
	document.addEventListener("click", (e) => {
		dropdownRegistry.forEach((dd) => {
			if (dd.open && !dd.trigger.parentElement.contains(e.target)) {
				dd.closeMenu();
			}
		});
	});

	document
		.querySelectorAll("[data-dropdown]")
		.forEach((dropdown) => initDropdownRaw(dropdown));
}

const dropdown_device = document.querySelector("#dropdown-device");
initDropdownRaw(dropdown_device, (trigger, menu, isOpen) => {
	trigger.querySelector(".arrow-drop").classList.toggle("rotate-180", !isOpen);
});
