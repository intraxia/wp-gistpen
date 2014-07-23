( function() {
	var gistpenid = '';

	// Register plugin
	tinymce.create( 'tinymce.plugins.wp_gistpen', {

		init: function( editor, url )  {

			// Add the Insert Gistpen button
			editor.addButton( 'wp_gistpen', {
				text: 'Insert Gistpen',
				tooltip: 'Insert Gistpen',
				cmd: 'wp_gistpen'
			});

			// Called when we click the Insert Gistpen button
			editor.addCommand( 'wp_gistpen', function() {
				editor.windowManager.open({
					title: 'Insert Gistpen',
					width: 450,
					height: 420,
					inline: 1,
					id: 'wp-gistpen',
					buttons: [{
						text: 'Insert',
						class: 'insert',
						onclick: function( e ) {
							editor.insertContent( '[gistpen id="' + gistpenid + '"]' );
							editor.windowManager.close();
						},
					},
					{
						text: 'Cancel',
						onclick: 'close'
					}],
				});

				// Show loading image while we wait
				jQuery( '#wp-gistpen-body' ).append( '<div class="loading">Loading <span class="spinner"></span></div>' );

				// Get the form template from WordPress
				jQuery.post( ajaxurl, {
					action: 'gistpen_insert'
				}, function( data ) {
					jQuery( '#wp-gistpen-body .loading' ).hide();
					jQuery( '#wp-gistpen-body' ).append( data );
					jQuery( '#wp-gistpen-body #search-results, .spinner' ).hide();
					jQuery( 'input[name="gistpen_id"]' ).click( function() {
						if( this.value != 'new_gistpen') {
							gistpenid = this.value;
						}
					});
				});

			});
		}

	});

	tinymce.PluginManager.add( 'wp_gistpen', tinymce.plugins.wp_gistpen );

})();
