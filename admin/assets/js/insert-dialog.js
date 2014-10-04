var InsertGistpenDialog = {

	appendDialog: function() {
		var that = this;
		this.dialogBody = jQuery('#wp-gistpen-insert-dialog-body');
		// HTML looks like this:
		// <div id="wp-gistpen-insert-wrap">
		// 	<form id="wp-gistpen-insert" action="" tabindex="-1">
		// 		<div id="insert-existing">
		// 			<p>Insert an existing Gistpen</p>
		// 			<div class="gistpen-search-wrap">
		// 				<label class="gistpen-search-label">
		// 					<label for="gistpen-search-field" class="search-label" style="display: none;">Search Gistpens</label>
		// 					<input type="search" id="gistpen-search-field" class="search-field" placeholder="Search Gistpens" />
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
		// 							<li id="wp-gistfile-wrap">
		// 							</li>
		// 						</ul>
		// 					</li>
		// 				</ul>
		// 			</div>
		// 		</div>
		// 	</form>
		// </div>
		this.dialogHTML = jQuery('<div id="wp-gistpen-insert-wrap"><form id="wp-gistpen-insert" action="" tabindex="-1"><div id="insert-existing"><p>Insert an existing Gistpen</p><div class="gistpen-search-wrap"><label class="gistpen-search-label"><label for="gistpen-search-field" class="search-label" style="display: none;">Search Gistpens</label><input type="search" id="gistpen-search-field" class="search-field" placeholder="Search Gistpens" /><div id="wp-gistpen-search-btn" class="mce-btn"><button role="button">Search</button><span class="spinner"></span></div></label></div><div id="select-gistpen" class="query-results"><div class="query-notice"><em>Recent Gistpens</em></div><ul class="gistpen-list"><!-- Add Gistpen list here --><li class="create_new_gistpen"><div class="gistpen-radio"><input type="radio" name="gistpen_id" value="new_gistpen" checked="checked"></div><div class="gistpen-title">Create a new Gistpen:</div><div class="clearfix"></div><ul><li><label for="wp-gistfile-description" style="display: none;">Gistpen description...</label><input type="text" name="wp-gistfile-description" class="wp-gistfile-description" placeholder="Gistpen description..."></input></li><li id="wp-gistfile-wrap"></li></ul></li></ul></div></div></form></div>');
		this.gistpenSearchButton = this.dialogHTML.find('#wp-gistpen-search-btn');

		this.dialogBody.append(this.dialogHTML);
		jQuery('.spinner').hide();

		this.fileEditor = new TinyMCEFileEditor();

		// Append recent Gistpens
		// @todo replace this with localize script
		this.getGistpens();

		this.gistpenSearchButton.click( function( e ) {
			$this = jQuery(this);
			e.preventDefault();

			jQuery('#select-gistpen ul.gistpen-list > li').not('.create_new_gistpen').remove();
			$this.children('button').hide();
			jQuery('.gistpen-search-wrap .spinner').show();

			that.getGistpens();

		});
	},

	getGistpens: function() {
		jQuery.post(ajaxurl, {
			action: 'get_gistpens',

			nonce: jQuery.trim(jQuery('#_ajax_wp_gistpen').val()),
			gistpen_search_term: jQuery('#gistpen-search-field').val()
		}, function(response) {
			if (response.success === false) {
				jQuery('<li><div class="gistpen-radio"><input type="radio" name="gistpen_id" value="none"></div><div class="gistpen-title">'+response.data.message+'</div></li>').prependTo('.gistpen-list');
			} else {
				var data = response.data;
				for (var i = data.gistpens.length - 1; i >= 0; i--) {
					var gistpen = data.gistpens[i];
					if ( typeof gistpen.description !== "undefined" ) {
						gistpen.post_name = gistpen.description;
					} else {
						gistpen.post_name = gistpen.slug;
					}
					jQuery('<li><div class="gistpen-radio"><input type="radio" name="gistpen_id" value="'+gistpen.ID+'"></div><div class="gistpen-title">'+gistpen.post_name+'</div></li>').prependTo('.gistpen-list');
				}
			}

			jQuery('.gistpen-search-wrap .spinner').hide();
			jQuery('#wp-gistpen-search-btn button').show();
		});
	},

	insertShortcode: function( editor ) {
		var that = this;
		// Get the selected gistpen id
		this.gistpenid = jQuery('input[name="gistpen_id"]:checked').val();

		if( this.gistpenid === 'new_gistpen' ) {
			// Hide the buttons and replace with spinner
			jQuery('#wp-gistpen-button-insert, #wp-gistpen-button-cancel').hide();
			jQuery('#wp-gistpen-insert-dialog .mce-foot .mce-container-body').append('<div class="posting">Inserting post... <span class="spinner"></span></div>');

			// Post the data
			jQuery.post( ajaxurl, {
				action: 'create_gistpen',

				nonce: jQuery.trim(jQuery('#_ajax_wp_gistpen').val()),

				"wp-gistpenfile-slug": that.fileEditor.filenameInput.val(),
				"wp-gistfile-description": jQuery('input.wp-gistfile-description').val(),
				"wp-gistpenfile-code": that.fileEditor.editorTextArea.val(),
				"wp-gistpenfile-language": that.fileEditor.languageSelect.val(),
				"post_status": that.fileEditor.post_status.val(),
			}, function( response ) {
				if ( response.success === false ) {
					editor.insertContent('Failed to save Gistpen. Message: '+response.data.message);
					editor.windowManager.close();
				} else {
					that.gistpenid = response.data.id;
					editor.insertContent( '[gistpen id="' + that.gistpenid + '"]' );
					editor.windowManager.close();
				}
			});
		} else {
			editor.insertContent( '[gistpen id="' + this.gistpenid + '"]' );
			editor.windowManager.close();
		}
	}
};
