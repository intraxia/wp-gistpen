function GistfileEditor(gistfileID) {
	this.gistfileID = gistfileID;
	this.aceEditorId = 'ace-editor-'+this.gistfileID;
	this.aceEditorDiv = jQuery('#' + this.aceEditorId);
	this.textButton = jQuery('#content-html-'+this.gistfileID);
	this.aceButton = jQuery('#content-ace-'+this.gistfileID);
	this.contentWrapDiv = jQuery('#wp-gistfile-content-'+this.gistfileID+'-wrap');
	this.contentDiv = jQuery('#gistfile-content-'+this.gistfileID);
	this.languageSelect = jQuery('#gistfile-language-'+this.gistfileID);
	this.deleteGistfileButton = jQuery('#delete-gistfile-'+this.gistfileID);

	this.init();
}

GistfileEditor.prototype  = {

	init: function() {
		jQuery('#titlediv .inside').remove();

		this.loadClickHandlers();
		this.activateAceEditor();
	},

	loadClickHandlers: function() {
		var theeditor = this;
		this.textButton.click(function(){
			theeditor.switchToText();
		});
		this.aceButton.click(function(){
			theeditor.switchToAce();
		});
		this.deleteGistfileButton.click(function(event) {
			event.preventDefault();
			theeditor.deleteEditor();
		});
	},

	switchToText: function() {
		this.aceEditorDiv.hide();
		this.contentWrapDiv.addClass('html-active').removeClass('ace-active');
		this.contentDiv.show();
	},

	switchToAce: function() {
		this.updateAceContent();
		this.aceEditorDiv.show();
		this.contentDiv.hide();
		this.contentWrapDiv.removeClass('html-active').addClass('ace-active');
		this.aceEditor.focus();
	},

	deleteEditor: function() {
		var theeditor = this;
		this.contentWrapDiv.remove();
		jQuery.post(ajaxurl,{
			action: 'delete_gistfile_editor',

			delete_editor_nonce: jQuery.trim(jQuery('#_ajax_wp_gistpen').val()),
			gistfileID: theeditor.gistfileID,
		}, function(response) {
			if(response === false) {
				console.log('Failed to delete Gistfile.');
			}
		});
	},

	activateAceEditor: function() {
		var theeditor = this;
		// Set up editor on div
		this.aceEditor = ace.edit(this.aceEditorId);
		this.setUpThemeAndMode();

		this.aceEditor.getSession().on('change', function(event) {
			theeditor.updateTextContent();
		});
		this.switchToAce();
	},

	setUpThemeAndMode: function() {
		var theeditor = this;
		this.aceEditor.setTheme('ace/theme/' + GistpenEditor.themeSelect.val());
		GistpenEditor.themeSelect.change(function() {
			theeditor.aceEditor.setTheme('ace/theme/' + GistpenEditor.themeSelect.val());
			jQuery.post(ajaxurl, {
				action: 'gistpen_save_ace_theme',

				theme_nonce: jQuery.trim(jQuery('#_ajax_wp_gistpen').val()),
				theme: theeditor.themeSelect.val(),

			}, function(response) {
				if(response === false) {
					console.log('Failed to save ACE theme.');
				}
			});
		});
		// Set mode and enable listener
		this.setMode(this.languageSelect.val());
		this.languageSelect.change(function() {
			theeditor.setMode(theeditor.languageSelect.val());
		});
	},

	setMode: function(mode) {
		if('js' === mode) {
			this.aceEditor.getSession().setMode('ace/mode/javascript');
		} else if( 'bash' === mode) {
			this.aceEditor.getSession().setMode('ace/mode/sh');
		} else {
			this.aceEditor.getSession().setMode('ace/mode/' + mode);
		}
	},

	updateAceContent: function() {
		this.aceEditor.getSession().setValue(this.contentDiv.val());
	},

	updateTextContent: function() {
		this.contentDiv.val(this.aceEditor.getValue());
	}
};
