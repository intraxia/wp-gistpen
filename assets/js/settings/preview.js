Gistpen.Preview = Backbone.Model.extend({

	initialize: function(opts) {
		this.cssLink = jQuery('link#wp-gistpen-prism-theme-css');
		this.themeSelect = jQuery('#_wpgp_gistpen_highlighter_theme');
		this.lnSelect = jQuery('#_wpgp_gistpen_line_numbers');
		this.pre = jQuery('pre.gistpen');
		this.lineNumbers = jQuery('span.line-numbers-rows');
	},

	setTheme: function() {
		var theme = this.themeSelect.val();

		if(theme == 'default') {
			theme = '';
		} else {
			theme = '-' + theme;
		}

		this.cssLink.attr('href', WP_GISTPEN_URL + 'assets/css/prism/themes/prism' + theme + '.css' );
	},

	setClickHandlers: function() {
		var that = this;

		this.setTheme();

		if(!this.lnSelect.is(':checked')) {
			this.pre.removeClass('line-numbers');
			this.lineNumbers.hide();
		}

		this.themeSelect.change(function() {
			that.setTheme();
		});

		this.lnSelect.click(function() {
			if(that.lnSelect.is(':checked')) {
				that.pre.addClass('line-numbers');
				that.lineNumbers.prependTo('pre.gistpen code');
				that.lineNumbers.show();
			} else {
				that.pre.removeClass('line-numbers');
				that.lineNumbers.hide();
			}
		});
	}
});
