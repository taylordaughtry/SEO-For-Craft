var analyzer = (function() {
	var analysisContainer = document.querySelectorAll('[data-ref="analysis"]')[0],
		items = [],
		responses = {
			idealTitle: 'Your title is an ideal length.',
			shortTitle: 'Your title is too short.',
			longTitle: 'Your title is too long.'
		};

	var _addItem = function(text, errorType) {
		var span = document.createElement('span');

		span.className = 'analysis__item ' + errorType;
		span.innerText = text;

		items.push(span);
	};

	var processTitle = function () {
		var title = document.getElementById('title'),
			length = title.value.length;

		if (length >= 40 && length <= 60) {
			_addItem(responses.idealTitle, 'positive');
		} else if (length < 40) {
			_addItem(responses.shortTitle, 'negative');
		} else if (length > 60) {
			_addItem(responses.longTitle, 'negative');
		}
	};

	var run = function () {
		analysisContainer.innerHTML = '';

		items = [];

		processTitle();

		items.forEach(function(el, i) {
			analysisContainer.appendChild(items[i]);
		});
	};

	return {
		processTitle: processTitle,
		run : run
	};
})();

analyzer.run();

setInterval(function() {
	analyzer.run();
}, 1000);