var app = (function() {
	/**
	 * Binds an input field with its corresponding preview element. Used in the
	 * 'Snippet Preview' field to show what a title/description look like.
	 *
	 * @private
	 * @param {string} name The input's name value
	 * @return void
	 */
	function _bindElement (name) {
		var $el = document.querySelectorAll('input[name*=' + name + ']')[0];

		$el.addEventListener('keyup', function() {
			var $el = document.querySelectorAll('[data-ref=' + name + ']')[0];

			$el.innerText = this.value;
		});
	}

	/**
	 * The native javascript version of $(document).ready(). Written here for
	 * simple usage.
	 * @private
	 * @param {function} callback The callback to be ran on ready
	 * @return void
	 */
	function _ready (callback) {
		document.addEventListener('DOMContentLoaded', function() {
			callback();
		});
	}

	/**
	 * Toggles a field's display, when provided with the name value.
	 * @private
	 * @param {string} fieldName The input's name value
	 * @return void
	 */
	function _toggleField (fieldName) {
		var $el = document.querySelectorAll('div.field[id*="' + fieldName + '"]')[0],
			currentDisplay = window.getComputedStyle($el, null).getPropertyValue('display');

		$el.style.display = currentDisplay !== 'none' ? 'none' : 'block';
	}

	/**
	 * Handles interaction when a particular twitter card type is selected.
	 *
	 * @private
	 * @param {string} value The select element's new value
	 * @return void
	 */
	function _handleTypes (value) {
		switch (value) {
			case 'summaryLargeImage':
				app.toggleField('twitterLargeImage');
				app.toggleField('twitterImage');
				break;
			case 'summary':
				app.toggleField('twitterLargeImage');
				app.toggleField('twitterImage');
				break;
		}
	}

	return {
		bind: _bindElement,
		handleTypes: _handleTypes,
		toggleField: _toggleField,
		ready: _ready
	}
})();

app.ready(function() {
	var twitterType = document.querySelectorAll('select[name*="twitterType"]')[0];

	app.bind('metaTitle');
	app.bind('metaDescription');

	if (twitterType.value === 'summmaryLargeImage') {
		app.toggleField('twitterLargeImage');
	} else {
		app.toggleField('twitterImage');
	}

	twitterType.addEventListener('change', function() {
		app.handleTypes(this.value);
	});
});