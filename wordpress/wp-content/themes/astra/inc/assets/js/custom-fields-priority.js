/**
 * custom-fields-priority.js
 *
 * Provide more preferences to Astra meta setting so wordpress custom field not causing any kind of conflicts.
 *
 * @package Astra
 */
function removeMetaBoxSection() {
	document.getElementById("the-list").remove();
}

function buttonClickEvent() {
	[...document.querySelectorAll('.editor-post-publish-button')].forEach(element => element.addEventListener('click', function (event) {
		removeMetaBoxSection();

	}));

	[...document.querySelectorAll('.editor-post-publish-panel__toggle')].forEach(element => element.addEventListener('click', function (event) {
		removeMetaBoxSection();
	}));
}

function DOMContentLoaded() {
	const elementMetaBox = document.getElementById("the-list");
	if (elementMetaBox != null) {
		setTimeout(buttonClickEvent, 2000);
	}
}

document.addEventListener('DOMContentLoaded', DOMContentLoaded, false);
