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
		// 						<div class="gistpen-title">Create a new Gistpen:</div>
		// 						<div class="clearfix"></div>
		// 						<ul>
		// 							<li>
		// 								<label for="wp-gistfile-description" style="display: none;">Gistpen description...</label>
		// 								<input type="text" name="wp-gistfile-description" class="wp-gistfile-description" placeholder="Gistpen description..."></input>
		// 							</li>
		// 							<li>
		// 								<label for="wp-gistpenfile-name" style="display: none;">Filename</label>
		// 								<input type="text" name="wp-gistpenfile-name" class="wp-gistpenfile-name" placeholder="Filename">
		// 							</li>
		// 							<li>
		// 								<label for="wp-gistpenfile-language" style="display: none;">Language</label>
		// 								<select class="wp-gistpenfile-language" name="wp-gistpenfile-language">
		// 									<!-- Insert languages here -->
		// 								</select>
		// 							</li>
		// 							<li>
		// 								<label for="post_status" style="display: none;">Post Status</label>
		// 								<select class="post_status" name="post_status">
		// 									<option value="publish">Published</option>
		// 									<option value="draft">Draft</option>
		// 								</select>
		// 							</li>
		// 							<li>
		// 								<label for="wp-gistpenfile-content" style="display: none;">Gistpen Content</label>
		// 								<textarea type="text" rows="5" name="wp-gistpenfile-content"></textarea>
		// 							</li>
		// 						</ul>
		// 					</li>
		// 				</ul>
		// 			</div>
		// 		</div>
		// 	</form>
		// </div>
		var dialog = jQuery('<div id="wp-gistpen-insert-wrap"><form id="wp-gistpen-insert" action="" tabindex="-1"><div id="insert-existing"><p>Insert an existing Gistpen</p><div class="gistpen-search-wrap"><label class="gistpen-search-label"><span class="search-label">Search Gistpens</span><input type="search" id="gistpen-search-field" class="search-field" /><div id="wp-gistpen-search-btn" class="mce-btn"><button role="button">Search</button><span class="spinner"></span></div></label></div><div id="select-gistpen" class="query-results"><div class="query-notice"><em>Recent Gistpens</em></div><ul class="gistpen-list"><!-- Add Gistpen list here --><li class="create_new_gistpen"><div class="gistpen-radio"><input type="radio" name="gistpen_id" value="new_gistpen" checked="checked"></div><div class="gistpen-title">Create a new Gistpen:</div><div class="clearfix"></div><ul><li><label for="wp-gistfile-description" style="display: none;">Gistpen description...</label><input type="text" name="wp-gistfile-description" class="wp-gistfile-description" placeholder="Gistpendescription..."></input></li><li><label for="wp-gistpenfile-name" style="display: none;">Filename</label><input type="text" name="wp-gistpenfile-name" class="wp-gistpenfile-name" placeholder="Filename"></li><li><label for="wp-gistpenfile-language" style="display: none;">Language</label><select class="wp-gistpenfile-language" name="wp-gistpenfile-language"><!-- Insert languages here --></select></li><li><label for="post_status" style="display: none;">Post Status</label><select class="post_status" name="post_status"><option value="publish">Published</option><option value="draft">Draft</option></select></li><li><label for="wp-gistpenfile-content" style="display: none;">Gistpen Content</label><textarea type="text" rows="5" name="wp-gistpenfile-content"></textarea></li></ul></li></ul></div></div></form></div>');
		var gistpenSearchButton = dialog.find('#wp-gistpen-search-btn');

		dialogBody.append(dialog);
		jQuery('.spinner').hide();
		// Append languages
		jQuery.post(ajaxurl,{
			action: 'get_gistpen_languages',

			nonce: jQuery.trim(jQuery('#_ajax_wp_gistpen').val()),
		}, function(response) {
			jQuery.each(response.data.languages, function(index, el) {
				jQuery('<option></option>').val(index).text(el).appendTo('.wp-gistpenfile-language');
			});
		});

		// Append recent Gistpens
		getGistpens();

		gistpenSearchButton.click( function( e ) {
			e.preventDefault();

			jQuery('#select-gistpen ul.gistpen-list > li').not('.create_new_gistpen').remove();
			gistpenSearchButton.children('button').hide();
			jQuery('.gistpen-search-wrap .spinner').show();

			getGistpens();

		});
	}

	function getGistpens() {
		jQuery.post(ajaxurl,{
			action: 'get_gistpens',

			nonce: jQuery.trim(jQuery('#_ajax_wp_gistpen').val()),
			gistpen_search_term: jQuery('#gistpen-search-field').val()
		}, function(response) {
			var data = response.data;
			for (var i = data.gistpens.length - 1; i >= 0; i--) {
				var gistpen = data.gistpens[i];
				jQuery('<li><div class="gistpen-radio"><input type="radio" name="gistpen_id" value="'+gistpen.id+'"></div><div class="gistpen-title">'+gistpen.post_name+'</div></li>').prependTo('.gistpen-list');
			}
			jQuery('.gistpen-search-wrap .spinner').hide();
			jQuery('#wp-gistpen-search-btn button').show();
		});
	}

	// Post? Then insert shortcode on click
	function insertShortcode(editor) {
		// Get the selected gistpen id
		gistpenid = jQuery('input[name="gistpen_id"]:checked').val();

		if( gistpenid === 'new_gistpen' ) {
			// Hide the buttons and replace with spinner
			jQuery('#wp-gistpen-button-insert, #wp-gistpen-button-cancel').hide();
			jQuery('#wp-gistpen-insert-dialog .mce-foot .mce-container-body').append('<div class="posting">Inserting post... <span class="spinner"></span></div>');

			// Post the data
			jQuery.post( ajaxurl, {
				action: 'create_gistpen',

				nonce: jQuery.trim(jQuery('#_ajax_wp_gistpen').val()),
				"wp-gistfile-description": jQuery('textarea[name="wp-gistfile-description"]').val(),
				"wp-gistpenfile-name": jQuery('input[name="wp-gistpenfile-name"]').val(),
				"wp-gistpenfile-content": jQuery('textarea[name="wp-gistpenfile-content"]').val(),
				"wp-gistpenfile-language": jQuery('select[name="wp-gistpenfile-language"]').val(),
				"post_status": jQuery('select[name="wp-gistpenfile-language"]').val(),
			}, function( response ) {
				gistpenid = response.data.id;
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
