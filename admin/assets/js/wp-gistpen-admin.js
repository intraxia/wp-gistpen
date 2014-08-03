(function () {
	"use strict";

	jQuery(function () {

		var theme = jQuery( '#_wpgp_gistpen_highlighter_theme' ).val();
		if( theme == 'default' ) {
			theme = '';
		} else {
			theme = '-' + theme;
		}
		jQuery( 'link#wp-gistpen-prism-style-theme-css' ).attr( 'href', WP_GISTPEN_URL + 'public/assets/vendor/prism/themes/prism' + theme + '.css'  );

		jQuery( '#_wpgp_gistpen_highlighter_theme' ).change( function() {
			var theme = jQuery( '#_wpgp_gistpen_highlighter_theme' ).val();
			if( theme == 'default' ) {
				theme = '';
			} else {
				theme = '-' + theme;
			}
			jQuery( '#wp-gistpen-prism-style-theme-css' ).attr( 'href', WP_GISTPEN_URL + 'public/assets/vendor/prism/themes/prism' + theme + '.css'  );
		});

		if (jQuery( '#_wpgp_gistpen_line_numbers' ).is(':checked')) {
			jQuery( 'pre.gistpen' ).addClass('line-numbers');
		}

		jQuery( '#_wpgp_gistpen_line_numbers' ).click(function() {
			if (jQuery( '#_wpgp_gistpen_line_numbers' ).is(':checked')) {
				jQuery( 'pre.gistpen' ).addClass('line-numbers');
			} else {
				jQuery( 'pre.gistpen' ).removeClass('line-numbers');
			}
		});
	});

}(jQuery));
