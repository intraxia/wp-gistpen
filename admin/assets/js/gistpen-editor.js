var GistpenEditor = {

	init: function(files) {
		this.themeSelect = jQuery('#_wpgp_ace_theme');
		this.addGistfileButton = jQuery('#add-gistfile');
		this.fileIDs = jQuery('#file_ids');
		this.editorWrap = jQuery('#wp-gistfile-wrap');
		this.files = files;
		this.editors = [];

		this.initEditors();
		this.loadClickHandlers();
	},

	initEditors: function() {
		for (var i = 0; this.files.length - 1 >= i; i++) {
			this.addEditor(this.files[i]);
		}
	},

	addEditor: function(file) {
		var theeditor = this;
		this.editors.push(new FileEditor(file));
		if(typeof file === 'undefined'|| typeof file.ID === 'undefined' || file.ID === null) {
			file = {};
			jQuery.ajax({
				type: "POST",
				url: ajaxurl,
				data: {
					action: 'get_gistpenfile_id',

					parent_id: jQuery('#post_ID').val(),
					nonce: theeditor.getNonce(),
				},
				success: function(response) {
					if(response.success === false) {
						console.log('Failed to get ID.');
						console.log(response.data.message);
					} else {
						file.ID = response.data.id;
						theeditor.editors[theeditor.editors.length - 1].fileID = file.ID;
						theeditor.editors[theeditor.editors.length - 1].addID();
						theeditor.fileIDs.val(theeditor.fileIDs.val() + ' ' + file.ID);
					}
				}
			});
		} else {
			this.fileIDs.val(this.fileIDs.val() + ' ' + file.ID);
		}
	},

	loadClickHandlers: function() {
		var theeditor = this;

		this.addGistfileButton.click(function(event) {
			event.preventDefault();
			theeditor.addEditor();
		});

		this.themeSelect.change(function() {
			jQuery.post(ajaxurl, {
				action: 'save_ace_theme',

				nonce: theeditor.getNonce(),
				theme: theeditor.themeSelect.val(),

			}, function(response) {
				if(response.success === false) {
					console.log('Failed to save ACE theme.');
					console.log(response.data.message);
				}
			});
		});
	},

	getNonce: function() {
		return jQuery.trim(jQuery('#_ajax_wp_gistpen').val());
	}

};
