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
			bodyLengthLow: 'Your content has {c} words. Minimum is 300.',
			densityFail: 'Your focus keyword isn\'t in your content.',
			densityLow: 'Your keyword density is too low. ({d}%)',
			densityOkay: 'Your keyword density is ideal. ({d}%)',
			densityHigh: 'Your keyword density is too high. ({d}%)',
			headingsPass: 'Your keyword is in a primary heading.',
			headingsAcceptable: 'Your keyword is in a secondary heading.',
			headingsFail: 'Your keyword isn\'t in any headings.',
			mainHeadingFails: 'You shouldn\'t have an H1 in your content.'
		};

	/**
	 * Adds an item to be displayed in the Analysis sidebar.
	 *
	 * @private
	 * @param {string} text The text to be displayed in this message.
	 * @param {string} scoreType The score class: positive/negative/acceptable
	 */
	var _addItem = function(text, scoreType) {
		var span = document.createElement('span');

		span.className = 'analysis__item ' + scoreType;
		span.innerText = text;

		items.push({
			score: scoreType,
			element: span
		});
	};

	var _sortItems = function() {
		var positive = [],
			negative = [],
			acceptable = [],
			i;

		for (i = 0; i < items.length; i++) {
			var obj = items[i];

			switch (obj.score) {
				case 'positive':
					positive.push(obj);
					break;
				case 'negative':
					negative.push(obj);
					break;
				case 'acceptable':
					acceptable.push(obj);
					break;
			}
		}

		items = negative.concat(acceptable.concat(positive));
	};

	/**
	 * Builds a document fragment for tag queries by adding a div with the
	 * content set as the innerHTML.
	 *
	 * @private
	 * @param {string} content The HTML template to be parsed
	 * @return {document} A valid document element
	 */
	var _buildFragment = function (content) {
		var frag = document.createDocumentFragment(),
			div = document.createElement('div');

		div.innerHTML = content;

		frag.appendChild(div);

		return frag;
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
	 * Check to ensure that at least a primary or secondary heading contains
	 * the focus keyword. Also confirm that there isn't an H1 in the content,
	 * since your entry title should ideally be the only H1 on the page.
	 *
	 * TODO: Check to ensure there's no H1 in the content. (Should only be 1.)
	 * TODO: Clarify heading messages.
	 * TODO: Abstract the field text retrieval process.
	 *
	 * @public
	 * @return void
	 */
	var processHeadings = function () {
		var content = $('#fields-body').redactor('code.get'),
			frag = _buildFragment(content),
			results = frag.querySelectorAll('h1,h2,h3,h4,h5,h6'),
			headings = [],
			primary = 0,
			secondary = 0,
			i;

		for (i = 0; i < results.length; i++) {
			var $el = results[i],
				nodeName = $el.nodeName;

			if ($el.textContent.toLowerCase().indexOf(focusKeyword) > -1) {
				if (nodeName === 'H2' || nodeName === 'H3') {
					primary++;
				} else {
					secondary++;
				}
			}

			if (nodeName === 'H1') {
				_addItem(responses.mainHeadingFails, 'negative');
			}
		}

		if (primary > 0) {
			_addItem(responses.headingsPass, 'positive');

			return;
		} else if (secondary > 0) {
			_addItem(responses.headingsAcceptable, 'acceptable');

			return;
		}

		_addItem(responses.headingsFail, 'negative');
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
	 * Checks how often the focus keyword appears in the body content. There's
	 * a general consensus that roughly 2.5% is the maximum acceptable density
	 * for 'optimization' of a certain keyword.
	 *
	 * In 2016, human-readable content is more important than keyword density,
	 * but this is included as a general guideline for making sure you actually
	 * USE your focus keyword inside your content.
	 *
	 * TODO: Abstract the field text retrieval process.
	 *
	 * @public
	 * @return void
	 */
	var checkDensity = function () {
		var content = $('#fields-body').redactor('code.get'),
			plainText = $('#fields-body').redactor('clean.getPlainText', content),
			words = plainText.split(/[^a-z0-9']+/i),
			count = 0,
			density = 0.00;

		words.forEach(function(word, i) {
			if (word.toLowerCase() === focusKeyword) {
				count++;
			}
		});

		density = ((count / words.length) * 100).toFixed(2);

		if (density === '0.00') {
			_addItem(responses.densityFail.replace('{d}', density), 'negative');
		} else if (density <= 2.2) {
			_addItem(responses.densityLow.replace('{d}', density), 'acceptable');
		} else if (density > 2.2 && density <= 2.5) {
			_addItem(responses.densityOkay.replace('{d}', density), 'positive');
		} else if (density > 2.5) {
			_addItem(responses.densityHigh.replace('{d}', density), 'negative');
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
			processHeadings();
			checkBodyLength();
			checkDensity();
		} else {
			_addItem(responses.noKeyword, 'negative');
		}

		_sortItems();

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