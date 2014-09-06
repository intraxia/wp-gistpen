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
						onclick: function( e ) {
							insertShortcode( editor );
						},
					},
					{
						text: 'Cancel',
						id: 'wp-gistpen-button-cancel',
						onclick: 'close'
					}],
				});

				appendDialog();

			});

		}

	});

	tinymce.PluginManager.add( 'wp_gistpen', tinymce.plugins.wp_gistpen );

	function appendDialog() {
		var dialogBody = jQuery('#wp-gistpen-insert-dialog-body');

		// HTML looks like this:
		// <div id="wp-gistpen-insert-wrap">
		// 	<form id="wp-gistpen-insert" action="" tabindex="-1">
		// 		<div id="insert-existing">
		// 			<p>Insert an existing Gistpen</p>
		// 			<div class="gistpen-search-wrap">
		// 				<label class="gistpen-search-label">
		// 					<span class="search-label">Search Gistpens</span>
		// 					<input type="search" id="gistpen-search-field" class="search-field" />
		// 					<div id="wp-gistpen-search-btn" class="mce-btn">
		// 						<button role="button">Search</button>
		// 						<span class="spinner"></span>
		// 					</div>
		// 				</label>
		// 			</div>
		// 			<div id="select-gistpen" class="query-results">
		// 				<div class="query-notice"><em>Recent Gistpens</em></div>
		// 				<ul class="gistpen-list">
		// 					<!-- Add Gistpen list here -->
		// 					<li class="create_new_gistpen">
		// 						<div class="gistpen-radio"><input type="radio" name="gistpen_id" value="new_gistpen" checked="checked"></div>
		// 						<div class="gistpen-title">Create a new Gistpen</div>
		// 						<div class="clearfix"></div>
		// 						<ul>
		// 							<li>
		// 								<label for="gistpen_title">Gistpen Title</label>
		// 								<input type="text" name="gistpen_title">
		// 							</li>
		// 							<li>
		// 								<label for="gistpen_language">Gistpen Language</label>
		// 								<select class="gistpen_language" name="gistpen_language"><!-- Insert languages here --></select>
		// 							</li>
		// 							<li>
		// 								<label for="gistpen_content">Gistpen Content</label>
		// 								<textarea type="text" rows="5" name="gistpen_content"></textarea>
		// 							</li>
		// 							<li>
		// 								<label for="gistpen_description">Gistpen Description</label>
		// 								<textarea type="text" rows="5" name="gistpen_description"></textarea>
		// 							</li>
		// 						</ul>
		// 					</li>
		// 				</ul>
		// 			</div>
		// 		</div>
		// 	</form>
		// </div>
		var dialog = jQuery('<div id="wp-gistpen-insert-wrap"><form id="wp-gistpen-insert" action="" tabindex="-1"><div id="insert-existing"><p>Insert an existing Gistpen</p><div class="gistpen-search-wrap"><label class="gistpen-search-label"><span class="search-label">Search Gistpens</span><input type="search" id="gistpen-search-field" class="search-field" /><div id="wp-gistpen-search-btn" class="mce-btn"><button role="button">Search</button><span class="spinner"></span></div></label></div><div id="select-gistpen" class="query-results"><div class="query-notice"><em>Recent Gistpens</em></div><ul class="gistpen-list"><!-- Add Gistpen list here --><li class="create_new_gistpen"><div class="gistpen-radio"><input type="radio" name="gistpen_id" value="new_gistpen" checked="checked"></div><div class="gistpen-title">Create a new Gistpen</div><div class="clearfix"></div><ul><li><label for="gistpen_title">Gistpen Title</label><input type="text" name="gistpen_title"></li><li><label for="gistpen_language">Gistpen Language</label><select class="gistpen_language" name="gistpen_language"><!-- Insert languages here --></select></li><li><label for="gistpen_content">Gistpen Content</label><textarea type="text" rows="5" name="gistpen_content"></textarea></li><li><label for="gistpen_description">Gistpen Description</label><textarea type="text" rows="5" name="gistpen_description"></textarea></li></ul></li></ul></div></div></form></div>');
		var gistpenSearchButton = dialog.find('#wp-gistpen-search-btn');

		dialogBody.append(dialog);
		jQuery('.spinner').hide();
		// Append languages
		jQuery.post(ajaxurl,{
			action: 'get_gistpen_languages',

			nonce: jQuery.trim(jQuery('#_ajax_wp_gistpen').val()),
		}, function(response) {
			debugger;
			jQuery.each(response.data.languages, function(index, el) {
				jQuery('<option></option>').val(index).text(el).appendTo('.gistpen_language');
			});
		});

		// Append recent Gistpens
		jQuery.post(ajaxurl,{
			action: 'get_recent_gistpens',

			nonce: jQuery.trim(jQuery('#_ajax_wp_gistpen').val()),
		}, function(response) {
			var data = response.data;
			for (var i = data.gistpens.length - 1; i >= 0; i--) {
				var gistpen = data.gistpens[i];
				jQuery('<li><div class="gistpen-radio"><input type="radio" name="gistpen_id" value="'+gistpen.id+'"></div><div class="gistpen-title">'+gistpen.post_name+'</div></li>').prependTo('.gistpen-list');
			}
		});

		gistpenSearchButton.click( function( e ) {
			e.preventDefault();

			jQuery( '#select-gistpen ul.gistpen-list > li' ).not( '.create_new_gistpen' ).hide();
			gistpenSearchButton.children( 'button' ).hide();
			jQuery( '.gistpen-search-wrap .spinner' ).show();

			jQuery.post( ajaxurl, {
				action: 'search_gistpen_ajax',

				nonce: jQuery.trim(jQuery( '#_ajax_wp_gistpen' ).val()),
				gistpen_search_term: jQuery( '#gistpen-search-field' ).val()
			}, function( response ) {
				jQuery( '#select-gistpen ul.gistpen-list' ).prepend( response );
				jQuery( '.gistpen-search-wrap .spinner' ).hide();
				gistpenSearchButton.children('button').show();
			});

		});
	}

	// Post? Then insert shortcode on click
	function insertShortcode( editor ) {
		// Get the selected gistpen id
		gistpenid = jQuery( 'input[name="gistpen_id"]:checked' ).val();

		if( gistpenid === 'new_gistpen' ) {
			// Hide the buttons and replace with spinner
			jQuery( '#wp-gistpen-button-insert, #wp-gistpen-button-cancel' ).hide();
			jQuery( '#wp-gistpen-insert-dialog .mce-foot .mce-container-body').append( '<div class="posting">Inserting post... <span class="spinner"></span></div>' );

			// Post the data
			jQuery.post( ajaxurl, {
				action: 'create_gistpen_ajax',

				nonce: jQuery.trim(jQuery('#_ajax_wp_gistpen').val()),
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
	}
	function insertAndClose( editor ) {
		editor.insertContent( '[gistpen id="' + gistpenid + '"]' );
		editor.windowManager.close();
	}

})();
