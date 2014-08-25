jQuery(function() {
	GistpenEditor.init();
});

var GistpenEditor = {

	init: function() {
		this.themeSelect = jQuery('#_wpgp_ace_theme');
		this.addGistfileButton = jQuery('#add-gistfile');
		this.gistfile_ids = jQuery('#gistfile_ids');

		this.loadClickHandlers();
	},

	loadClickHandlers: function() {
		var thiseditor = this;
		this.addGistfileButton.click(function(event) {
			event.preventDefault();
			thiseditor.addEditor();
		});
	},

	addEditor: function() {
		var thiseditor = this;
		jQuery.post(ajaxurl, {
			action: 'add_gistfile_editor',

			add_editor_nonce: jQuery.trim(jQuery('#_ajax_wp_gistpen').val()),
		}, function(response) {
			var neweditor = jQuery(response);
			var gistfileID = neweditor.find('#gistfile-id');
			var gistfileIDVal = gistfileID.val();

			neweditor.insertBefore(thiseditor.addGistfileButton.parent('.submit'));
			thiseditor.gistfile_ids.val(thiseditor.gistfile_ids.val() + ' ' + gistfileIDVal);
			window['gfe' + gistfileIDVal] = new GistfileEditor(gistfileIDVal);
		});
	}
};
