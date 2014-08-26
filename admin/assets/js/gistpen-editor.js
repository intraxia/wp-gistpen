jQuery(function() {
	GistpenEditor.init();
});

var GistpenEditor = {

	init: function() {
		this.themeSelect = jQuery('#_wpgp_ace_theme');
		this.addGistfileButton = jQuery('#add-gistfile');
		this.file_ids = jQuery('#file_ids');

		this.loadClickHandlers();
	},

	loadClickHandlers: function() {
		var thiseditor = this;
		this.addGistfileButton.click(function(event) {
			event.preventDefault();
			thiseditor.addEditor();
		});
		this.themeSelect.change(function() {
			jQuery.post(ajaxurl, {
				action: 'gistpen_save_ace_theme',

				theme_nonce: jQuery.trim(jQuery('#_ajax_wp_gistpen').val()),
				theme: thiseditor.themeSelect.val(),

			}, function(response) {
				if(response === false) {
					console.log('Failed to save ACE theme.');
				}
			});
		});
	},

	addEditor: function() {
		var thiseditor = this;
		jQuery.post(ajaxurl, {
			action: 'add_gistfile_editor',

			add_editor_nonce: jQuery.trim(jQuery('#_ajax_wp_gistpen').val()),
		}, function(response) {
			thiseditor.file_ids.val(thiseditor.file_ids.val() + ' ' + response);
			window['gfe' + response] = new FileEditor(response);
		});
	}
};
