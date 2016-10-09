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
		};

	var _addItem = function(text, errorType) {
		var span = document.createElement('span');

		span.className = 'analysis__item ' + errorType;
		span.innerText = text;

		items.push(span);
	};

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

	var processDescription = function () {
		var description = document.querySelectorAll('input[id*=metaDescription]')[0].value.toLowerCase();

		if (description.indexOf(focusKeyword) > -1) {
			_addItem(responses.keywordInDesc, 'positive');
		} else {
			_addItem(responses.keywordNotInDesc, 'negative');
		}
	};

	var processSlug = function () {
		var slug = document.getElementById('slug').value.toLowerCase();

		if (slug.indexOf(focusKeyword) > -1) {
			_addItem(responses.keywordInSlug, 'positive');
		} else {
			_addItem(responses.keywordNotInSlug, 'negative');
		}
	};

	var run = function () {
		var keywordInput = document.querySelectorAll('[data-ref="focusKeyword"]')[0];

		analysisContainer.innerHTML = '';

		items = [];

		if (keywordInput.value.length) {
			focusKeyword = keywordInput.value.toLowerCase();

			processTitle();
			processDescription();
			processSlug();
		} else {
			_addItem(responses.noKeyword, 'negative');
		}

		items.forEach(function(el, i) {
			analysisContainer.appendChild(items[i]);
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