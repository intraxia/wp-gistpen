( function() {
	// Register plugin
	tinymce.create( 'tinymce.plugins.wp_gistpen', {

		init: function( editor, url )  {

			editor.addButton( 'wp_gistpen', {
				text: 'Insert Gistpen',
				tooltip: 'Insert Gistpen',
				cmd: 'wp_gistpen'
			});

			editor.addCommand( 'wp_gistpen', function() {
				editor.windowManager.open({
					title: 'Insert Gistpen',
					width: 450,
					height: 420,
					inline: 1,
					id: 'wp-gistpen',
				});

				jQuery.post( ajaxurl, {
					action: 'gistpen_insert'
				}, function( data ) {
					jQuery( '#wp-gistpen-body' ).append( data );
				});
			});
		}

	});

	tinymce.PluginManager.add( 'wp_gistpen', tinymce.plugins.wp_gistpen );

})();
