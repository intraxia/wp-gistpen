( function () {
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

		var checkExist = setInterval( function() {
			if (jQuery( 'pre.gistpen code' ).length) {
				if (!jQuery( '#_wpgp_gistpen_line_numbers' ).is(':checked')) {
					jQuery( 'pre.gistpen' ).removeClass('line-numbers');
					jQuery( 'span.line-numbers-rows' ).hide();
				}
				clearInterval(checkExist);
			}
		}, 100 ); // check every 100ms

		jQuery( '#_wpgp_gistpen_line_numbers' ).click( function() {
			if (jQuery( '#_wpgp_gistpen_line_numbers' ).is( ':checked' ) ) {
				jQuery( 'pre.gistpen' ).addClass(' line-numbers' );
				jQuery( 'span.line-numbers-rows' ).prependTo( 'pre.gistpen code' );
				jQuery( 'span.line-numbers-rows' ).show();
			} else {
				jQuery( 'pre.gistpen' ).removeClass('line-numbers');
				jQuery( 'span.line-numbers-rows' ).hide();
			}
		});
	});

}(jQuery));
