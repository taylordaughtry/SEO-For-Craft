var fieldHandler = (function() {
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
		var $el = document.querySelectorAll('[data-ref="' + fieldName + '"]')[0],
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
				fieldHandler.toggleField('twitterLargeImage');
				fieldHandler.toggleField('twitterImage');
				break;
			case 'summary':
				fieldHandler.toggleField('twitterLargeImage');
				fieldHandler.toggleField('twitterImage');
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

fieldHandler.ready(function() {
	var twitterType = document.querySelectorAll('select[name*="twitterType"]')[0];

	fieldHandler.bind('metaTitle');
	fieldHandler.bind('metaDescription');

	if (twitterType.value === 'summmaryLargeImage') {
		fieldHandler.toggleField('twitterImage');
	} else {
		fieldHandler.toggleField('twitterLargeImage');
	}

	twitterType.addEventListener('change', function() {
		fieldHandler.handleTypes(this.value);
	});
});