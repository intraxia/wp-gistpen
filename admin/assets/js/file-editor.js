function FileEditor(gistfileID, gistfileName, gistfileContent, gistfileLanguage) {
	this.gistfileID = gistfileID;
	this.gistfileName = typeof gistfileName !== 'undefined' ? gistfileName : '';
	this.gistfileContent = typeof gistfileContent !== 'undefined' ? gistfileContent : '';
	this.gistfileLanguage = typeof gistfileLanguage !== 'undefined' ? gistfileLanguage : null;
	this.editorWrap = jQuery('#wp-gistfile-editor-wrap');
	this.editorHTML = '\
		<div class="wp-core-ui wp-editor-wrap wp-gistfile-content-wrap" id="wp-gistfile-content-'+this.gistfileID+'-wrap">\
			<div class="wp-editor-tools hide-if-no-js" id="wp-gistfile-content-'+this.gistfileID+'-editor-tools">\
\
				<div class="wp-media-buttons" id="wp-gistfile-content-'+this.gistfileID+'-media-buttons">\
					<label for="gistfile-name-'+this.gistfileID+'" style="display: none;">Gistfilename</label>\
					<input type="text" name="gistfile-name-'+this.gistfileID+'" size="20" class="gistfile-name" id="gistfile-name-'+this.gistfileID+'" value="'+this.gistfileName+'" placeholder="Filename (no ext)" autocomplete="off" />\
					<select name="gistfile-language-'+this.gistfileID+'" id="gistfile-language-'+this.gistfileID+'" class="gistfile-language"></select>\
					<input type="submit" name="delete-gistfile-'+this.gistfileID+'" id="delete-gistfile-'+this.gistfileID+'" class="button delete" value="Delete This Gistfile">\
				</div>\
\
				<div class="wp-editor-tabs" id="wp-editor-tabs-'+this.gistfileID+'">\
					<a class="hide-if-no-js wp-switch-editor switch-html" id="content-html-'+this.gistfileID+'">Text</a>\
					<a class="hide-if-no-js wp-switch-editor switch-ace" id="content-ace-'+this.gistfileID+'">Ace</a>\
				</div>\
\
			</div>\
\
			<div class="wp-editor-container" id="wp-gistfile-content-'+this.gistfileID+'-editor-container">\
				<textarea class="wp-editor-area" cols="40" id="gistfile-content-'+this.gistfileID+'" name="gistfile-content-'+this.gistfileID+'" rows="20">'+this.gistfileContent+'</textarea>\
				<div class="ace-editor" id="ace-editor-'+this.gistfileID+'"></div>\
			</div>\
\
			<input type="hidden" name="gistfile-id" id="gistfile-id" value="'+this.gistfileID+'">\
\
		</div>';
	this.editorFull = jQuery(this.editorHTML);
	this.aceEditorId = 'ace-editor-'+this.gistfileID;
	this.aceEditorDiv = this.editorFull.find('#' + this.aceEditorId);
	this.textButton = this.editorFull.find('#content-html-'+this.gistfileID);
	this.aceButton = this.editorFull.find('#content-ace-'+this.gistfileID);
	this.contentWrapDiv = this.editorFull.find('#wp-gistfile-content-'+this.gistfileID+'-wrap');
	this.contentDiv = this.editorFull.find('#gistfile-content-'+this.gistfileID);
	this.languageSelect = this.editorFull.find('#gistfile-language-'+this.gistfileID);
	this.deleteGistfileButton = this.editorFull.find('#delete-gistfile-'+this.gistfileID);

	this.init();
}

FileEditor.prototype  = {

	init: function() {

		jQuery('#titlediv .inside').remove();
		this.appendEditor();
		this.loadClickHandlers();
		this.activateAceEditor();
	},

	appendEditor: function() {
		this.editorFull.appendTo(this.editorWrap);
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
		GistpenEditor.themeSelect.change(function(event) {
			theeditor.aceEditor.setTheme('ace/theme/' + GistpenEditor.themeSelect.val());
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
