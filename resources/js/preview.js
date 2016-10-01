function bindElement (name) {
	var query = document.querySelectorAll('input[name*=' + name + ']');

	query[0].addEventListener('keyup', function() {
		var el = document.querySelectorAll('[data-ref=' + name + ']');

		el[0].innerText = this.value;
	});
}

document.addEventListener('DOMContentLoaded', function() {
	bindElement('metaTitle');
	bindElement('metaDescription');
});