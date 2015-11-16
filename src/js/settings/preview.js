module.exports = (function ($) {
	var cssLink;
	var themeSelect;
	var lnSelect;
	var pre;
	var lineNumbers;

	return {
		init: init
	};

	function init() {
		queryDOM();
		setTheme();
		toggleLineNumbers();
		setClickHandlers();
	}

	function queryDOM() {
		cssLink = $('link').filter(function() {
			return $(this).attr('href').indexOf('wp-gistpen/assets/css/prism/themes') !== -1;
		});
		themeSelect = $('#_wpgp_gistpen_highlighter_theme');
		lnSelect = $('#_wpgp_gistpen_line_numbers');
		pre = $('pre.gistpen');
		lineNumbers = $('span.line-numbers-rows');
	}

	function setTheme() {
		var theme = themeSelect.val();

		if (theme == 'default') {
			theme = '';
		} else {
			theme = '-' + theme;
		}

		cssLink.attr('href', WP_GISTPEN_URL + 'assets/css/prism/themes/prism' + theme + '.css');
	}

	function setClickHandlers() {
		themeSelect.change(setTheme);
		lnSelect.click(toggleLineNumbers);
	}

	function toggleLineNumbers() {
		if (lnSelect.is(':checked')) {
			pre.addClass('line-numbers');
			lineNumbers.prependTo('pre.gistpen code');
			lineNumbers.show();
		} else {
			pre.removeClass('line-numbers');
			lineNumbers.hide();
		}
	}

})(window.jQuery);
