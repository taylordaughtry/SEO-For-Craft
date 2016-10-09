var analyzer = (function() {
	var analysisContainer = document.querySelectorAll('[data-ref="analysis"]')[0],
		items = [],
		focusKeyword = '',
		responses = {
			idealTitle: 'Your title is an ideal length.',
			shortTitle: 'Your title is too short.',
			longTitle: 'Your title is too long.',
			noTitleKeyword: 'Your keyword isn\'t in the title.',
			titleKeyword: 'Your keyword appears in the title.',
			noKeyword: 'You don\'t have a focus keyword set.',
			keywordInSlug: 'Your keyword is present in the URL.',
			keywordNotInSlug: 'Your keyword isn\'t in the URL.',
			keywordInDesc: 'Your keyword appears in the meta description.',
			keywordNotInDesc: 'Your keyword isn\'t in the meta description.',
			bodyLengthOkay: 'Your content has at least 300 words.',
			bodyLengthLow: 'Your content has {c} words. Minimum is 300.'
		};

	/**
	 * Adds an item to be displayed in the Analysis sidebar.
	 *
	 * @private
	 * @param {string} text The text to be displayed in this message.
	 * @param {string} errorType The error class: positive/negative/acceptable
	 */
	var _addItem = function(text, errorType) {
		var span = document.createElement('span');

		span.className = 'analysis__item ' + errorType;
		span.innerText = text;

		items.push({
			score: errorType,
			element: span
		});
	};

	/**
	 * Handles title processing, which includes length checks as well as
	 * checking for the inclusion of the focus keyword.
	 *
	 * @public
	 * @return void
	 */
	var processTitle = function () {
		var title = document.getElementById('title').value.toLowerCase(),
			length = title.length;

		if (length >= 40 && length <= 60) {
			_addItem(responses.idealTitle, 'positive');
		} else if (length < 40) {
			_addItem(responses.shortTitle, 'negative');
		} else if (length > 60) {
			_addItem(responses.longTitle, 'negative');
		}

		if (title.indexOf(focusKeyword) > -1) {
			_addItem(responses.titleKeyword, 'positive');
		} else {
			_addItem(responses.noTitleKeyword, 'negative');
		}
	};

	/**
	 * Handles description processing, which include checking for the presence
	 * of the focus keyword.
	 *
	 * @public
	 * @return void
	 */
	var processDescription = function () {
		var description = document.querySelectorAll('input[id*=metaDescription]')[0].value.toLowerCase();

		if (description.indexOf(focusKeyword) > -1) {
			_addItem(responses.keywordInDesc, 'positive');
		} else {
			_addItem(responses.keywordNotInDesc, 'negative');
		}
	};

	/**
	 * Handles slug processing, which includes checking for the presence of the
	 * focus keyword.
	 *
	 * @public
	 * @return void
	 */
	var processSlug = function () {
		var slug = document.getElementById('slug').value.toLowerCase();

		if (slug.indexOf(focusKeyword) > -1) {
			_addItem(responses.keywordInSlug, 'positive');
		} else {
			_addItem(responses.keywordNotInSlug, 'negative');
		}
	};

	/**
	 * Handles body processing, which includes checking for proper content
	 * length. Right now this method gets the field content via the Redactor
	 * API, which is a little dirty, but gets the job done.
	 *
	 * TODO: Is there a cleaner way to get the body textContent?
	 * TODO: Get the proper namespaced ID of the body element
	 *
	 * @public
	 * @return void
	 */
	var checkBodyLength = function () {
		var content = $('#fields-body').redactor('code.get'),
			plainText = $('#fields-body').redactor('clean.getPlainText', content),
			wordCount = textstatistics(plainText).wordCount();

		if (wordCount > 300) {
			_addItem(responses.bodyLengthOkay, 'positive');
		} else {
			_addItem(responses.bodyLengthLow.replace('{c}', wordCount), 'negative');
		}
	};

	/**
	 * Initializes the content analyzation process. This method is called once
	 * every 1,000 ms; NOT on input changes. After trying both ways, this
	 * method is not only simpler, but a bit more performant.
	 *
	 * @public
	 * @return void
	 */
	var run = function () {
		var keywordInput = document.querySelectorAll('[data-ref="focusKeyword"]')[0];

		analysisContainer.innerHTML = '';

		items = [];

		if (keywordInput.value.length) {
			focusKeyword = keywordInput.value.toLowerCase();

			processTitle();
			processDescription();
			processSlug();
			checkBodyLength();
		} else {
			_addItem(responses.noKeyword, 'negative');
		}

		items.forEach(function(el, i) {
			var item = items[i];

			analysisContainer.appendChild(item.element);
		});
	};

	return {
		processTitle: processTitle,
		run : run
	};
})();

document.addEventListener('DOMContentLoaded', function() {
	analyzer.run();

	setInterval(function() {
		analyzer.run();
	}, 1000);
});