TinyMCEFileEditor.prototype = Object.create(FileEditor.prototype);
TinyMCEFileEditor.prototype.constructor = TinyMCEFileEditor;

function TinyMCEFileEditor() {
	this.scaffoldEditor();
	this.appendEditor();
	this.appendPostStatusSelector();
	this.loadClickHandlers();
	this.deleteFileButton.remove();
	this.textButton.remove();
	this.aceButton.remove();
	this.aceEditorDiv.remove();
}

TinyMCEFileEditor.prototype.setTheme = function() {
		this.aceEditor.setTheme('ace/theme/twilight');
	};

TinyMCEFileEditor.prototype.loadClickHandlers = function() {
	var thiseditor = this;

	this.languageSelect.change(function() {
		thiseditor.setMode();
	});
};

TinyMCEFileEditor.prototype.appendEditor = function() {
	this.editorFull.appendTo(jQuery('#wp-gistfile-wrap'));
	this.appendLanguages();
	// add label: <label for="wp-gistpenfile-name-'+this.fileID+'" style="display: none;">Gistfilename</label>\
};

TinyMCEFileEditor.prototype.appendPostStatusSelector = function() {
	this.post_status = jQuery('<label for="post_status" style="display: none;">Post Status</label><select class="post_status" name="post_status"><option value="publish">Published</option><option value="draft">Draft</option></select>');
	this.post_status.appendTo(this.editorFull.find('.wp-editor-tools'));
	this.post_status = jQuery('select.post_status');
};

TinyMCEFileEditor.prototype.setMode = function() {

};
