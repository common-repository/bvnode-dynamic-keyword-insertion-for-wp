(function ($) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

})(jQuery);


function normalize(content) {
	const div = document.createElement('div');
	div.innerHTML = content;
	return div.innerHTML;
}

if (document.querySelector('[data-bvnode-dki4wp]')) {
	$items = Array.from(document.querySelectorAll('[data-bvnode-dki4wp]'));
	fetch('/wp-json/dki4wp/v1/shortcodes/' + ((window.location.search) ? window.location.search + '&' : '?') + 'random=' + ((new Date()).getTime() * Math.floor(Math.random() * 10000)) + '&shortcodes=' + JSON.stringify($items.reduce((arr, value) => { arr.push(value.getAttribute('data-bvnode-dki4wp')); return arr; }, [])))
		.then(response => response.json())
		.then((json) => {
			const values = json.values;
			$items.forEach((item, index) => {
				if (item.innerHTML + '' != normalize(values[index])) {
					item.innerHTML = normalize(values[index]);
				}

			});
		})
}

if (document.querySelector('iframe[data-defer],video[data-defer]')) {
	Array.from(document.querySelectorAll('iframe[data-defer],video[data-defer]')).forEach((item) => {
		const defer = item.getAttribute('data-defer');
		
			window.addEventListener('load', () => {
				if (defer == '') {
					item.src = item.getAttribute('data-src');
					item.removeAttribute('data-src');
				} else {
					setTimeout(() => {
						item.src = item.getAttribute('data-src');
						item.removeAttribute('data-src');
					}, defer);
				}
			}, {once: true});
		
	});
}