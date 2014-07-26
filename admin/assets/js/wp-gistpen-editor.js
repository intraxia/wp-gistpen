( function() {
	var template, gistpenid, title, content, description, language;

	// Register plugin
	tinymce.create( 'tinymce.plugins.wp_gistpen', {

		init: function( editor, url )  {

			// Add the Insert Gistpen button
			editor.addButton( 'wp_gistpen', {
				//text: 'Insert Gistpen',
				icon: 'icons dashicons-edit',
				tooltip: 'Insert Gistpen',
				cmd: 'wp_gistpen'
			});

			// Called when we click the Insert Gistpen button
			editor.addCommand( 'wp_gistpen', function() {
				// Calls the pop-up modal
				editor.windowManager.open({
					// Modal settings
					title: 'Insert Gistpen',
					width: jQuery( window ).width() * 0.7,
					// minus head and foot of dialog box
					height: (jQuery( window ).height() - 36 - 50) * 0.7,
					inline: 1,
					id: 'wp-gistpen-insert-dialog',
					buttons: [{
						text: 'Insert',
						id: 'wp-gistpen-button-insert',
						class: 'insert',
						// Post? Then insert shortcode on click
						onclick: function( e ) {
							// Get the selected gistpen id
							gistpenid = jQuery( 'input[name="gistpen_id"]:checked' ).val();

							if( gistpenid === 'new_gistpen' ) {
								// Hide the buttons and replace with spinner
								jQuery( '#wp-gistpen-button-insert, #wp-gistpen-button-cancel' ).hide();
								jQuery( '#wp-gistpen-insert-dialog .mce-foot .mce-container-body').append( '<div class="posting">Inserting post... <span class="spinner"></span></div>' );

								// Post the data
								jQuery.post( ajaxurl, {
									action: 'create_gistpen_ajax',

									gistpen_nonce: jQuery.trim(jQuery('#_ajax_wp_gistpen').val()),
									gistpen_title: jQuery( 'input[name="gistpen_title"]' ).val(),
									gistpen_content: jQuery( 'textarea[name="gistpen_content"]' ).val(),
									gistpen_description: jQuery( 'textarea[name="gistpen_description"]' ).val(),
									gistpen_language: jQuery( 'select[name="gistpen_language"]' ).val(),

								}, function( response ) {
									gistpenid = response;
									insertAndClose( editor );
								});
							} else {
								insertAndClose( editor );
							}
						},
					},
					{
						text: 'Cancel',
						id: 'wp-gistpen-button-cancel',
						onclick: 'close'
					}],
				});

				var dialogBody = jQuery( '#wp-gistpen-insert-dialog-body' ).append( '<div class="loading">Loading... <span class="spinner"></span></div>' );

				// Get the form template from WordPress
				jQuery.post( ajaxurl, {
					action: 'gistpen_insert_dialog'
				}, function( response ) {
					template = response;

					dialogBody.children( '.loading' ).remove();
					dialogBody.append( template );
					jQuery( '.spinner' ).hide();

					var gistpenSearchButton = jQuery( '#gistpen-search-btn' );

					gistpenSearchButton.click( function( e ) {
						e.preventDefault();

						jQuery( '#select-gistpen ul.gistpen-list > li' ).not( '.create_new_gistpen' ).hide();
						gistpenSearchButton.children( 'button' ).hide();
						jQuery( '.gistpen-search-wrap .spinner' ).show();

						jQuery.post( ajaxurl, {
							action: 'search_gistpen_ajax',

							gistpen_nonce: jQuery.trim(jQuery( '#_ajax_wp_gistpen' ).val()),
							gistpen_search_term: jQuery( '#gistpen-search-field' ).val()
						}, function( response ) {
							jQuery( '#select-gistpen ul.gistpen-list' ).prepend( response );
							jQuery( '.gistpen-search-wrap .spinner' ).hide();
							gistpenSearchButton.children('button').show();
						});

					});
				});

			});

		}

	});

	tinymce.PluginManager.add( 'wp_gistpen', tinymce.plugins.wp_gistpen );

	function insertAndClose( editor ) {
		editor.insertContent( '[gistpen id="' + gistpenid + '"]' );
		editor.windowManager.close();
	}

})();
