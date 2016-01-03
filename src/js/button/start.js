( function() {
	// Register plugin
	tinymce.create( 'tinymce.plugins.wp_gistpen', {

		init: function( editor, url )  {
			// Add the Insert Gistpen button
			editor.addButton( 'wp_gistpen', {
				//text: 'Insert Gistpen',
				icon: 'icons dashicons-editor-code',
				tooltip: 'Insert Gistpen',
				cmd: 'wp_gistpen'
			});

			// Called when we click the Insert Gistpen button
			editor.addCommand( 'wp_gistpen', function() {
				// Calls the pop-up modal
				editor.windowManager.open({
					// Modal settings
					title: 'Insert Gistpen',
					width: jQuery( window ).width() * 0.85,
					// minus head and foot of dialog box
					height: (jQuery( window ).height() - 36 - 50) * 0.85,
					inline: 1,
					id: 'wp-gistpen-insert-dialog',
					buttons: [{
						text: 'Insert',
						id: 'wp-gistpen-button-insert',
						class: 'insert',
						onclick: function( e ) {
							InsertGistpenDialog.insertShortcode( editor );
						},
					},
					{
						text: 'Cancel',
						id: 'wp-gistpen-button-cancel',
						onclick: 'close'
					}],
				});

				InsertGistpenDialog.appendDialog();

			});

		}

	});

	tinymce.PluginManager.add( 'wp_gistpen', tinymce.plugins.wp_gistpen );

})();
