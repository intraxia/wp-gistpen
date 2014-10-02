function FileEditor(file) {
	this.file = typeof file !== "undefined" ? file : {};
	this.scaffoldEditor();
	this.appendEditor();

	this.fileID = typeof this.file.ID !== "undefined" ? this.file.ID : '';
	this.addID();

	this.fileName = typeof this.file.slug !== "undefined" ? this.file.slug : '';
	this.addName();

	this.fileContent = typeof this.file.code !== "undefined" ? this.file.code : '';
	this.addContent();

	this.fileLanguage = (typeof this.file.language !== "undefined" && typeof this.file.language.slug !== "undefined") ? (this.file.language.slug === "javascript" ? "js" : this.file.language.slug) : 'bash';
	this.addLanguage();

	this.activateAceEditor();
	this.loadClickHandlers();

}

FileEditor.prototype  = {

	scaffoldEditor: function() {
		//	HTML looks like this:
		//	<div class="wp-core-ui wp-editor-wrap wp-gistpenfile-editor-wrap">
		//		<div class="wp-editor-tools hide-if-no-js">
		//
		//			<div class="wp-media-buttons">
		//				<input type="text" size="20" class="wp-gistpenfile-slug" placeholder="Filename (no ext)" autocomplete="off" />
		//				<select class="wp-gistpenfile-language"></select>
		//				<input type="submit" class="button delete" value="Delete This Gistfile">
		//			</div>
		//
		//			<div class="wp-editor-tabs wp-gistpenfile-editor-tabs">
		//				<a class="hide-if-no-js wp-switch-editor switch-html">Text</a>
		//				<a class="hide-if-no-js wp-switch-editor switch-ace">Ace</a>
		//			</div>
		//
		//		</div>
		//
		//		<div class="wp-editor-container wp-gistpenfile-editor-container">
		//			<textarea class="wp-editor-area wp-gistpenfile-editor-area" cols="40" rows="20"></textarea>
		//			<div class="ace-editor"></div>
		//		</div>
		//
		//	</div>
		this.editorHTML = '<div class="wp-core-ui wp-editor-wrap wp-gistpenfile-editor-wrap"><div class="wp-editor-tools hide-if-no-js"><div class="wp-media-buttons"><input type="text" size="20" class="wp-gistpenfile-slug" placeholder="Filename (no ext)" autocomplete="off" /><select class="wp-gistpenfile-language"></select><input type="submit" class="button delete" value="Delete This Gistfile"></div><div class="wp-editor-tabs wp-gistpenfile-editor-tabs"><a class="hide-if-no-js wp-switch-editor switch-html">Text</a><a class="hide-if-no-js wp-switch-editor switch-ace">Ace</a></div></div><div class="wp-editor-container wp-gistpenfile-editor-container"><textarea class="wp-editor-area wp-gistpenfile-editor-area" cols="40" rows="20"></textarea><div class="ace-editor"></div></div></div>';
		this.editorFull = jQuery(this.editorHTML);
		this.editorWrap = this.editorFull.find('.wp-gistpenfile-editor-wrap');
		this.editorTools = this.editorFull.find('.wp-editor-tools');
		this.mediaButtons = this.editorFull.find('.wp-media-buttons');
		this.filenameInput = this.editorFull.find('.wp-gistpenfile-slug');
		this.languageSelect = this.editorFull.find('.wp-gistpenfile-language');
		this.deleteFileButton = this.editorFull.find('.button.delete');
		this.editorTabs = this.editorFull.find('.wp-gistpenfile-editor-tabs');
		this.textButton = this.editorFull.find('.wp-switch-editor.switch-html');
		this.aceButton = this.editorFull.find('.wp-switch-editor.switch-ace');
		this.editorContainer = this.editorFull.find('.wp-gistpenfile-editor-container');
		this.editorTextArea = this.editorFull.find('.wp-gistpenfile-editor-area');
		this.aceEditorDiv = this.editorFull.find('.ace-editor');
	},

	appendEditor: function() {
		this.editorFull.appendTo(GistpenEditor.editorWrap);
		this.appendLanguages();
		// add label: <label for="wp-gistpenfile-slug-'+this.fileID+'" style="display: none;">Gistfilename</label>\
	},

	appendLanguages: function() {
		var thiseditor = this;
		jQuery.each(gistpenLanguages, function(index, el) {
			jQuery('<option></option>').val(index).text(el).appendTo(thiseditor.languageSelect);
		});
	},

	activateAceEditor: function() {
		var thiseditor = this;
		jQuery.each(this.aceEditorDiv, function(index, el) {
			thiseditor.aceEditor = el; // Get DOM object from jQuery
		});
		this.aceEditor = ace.edit(this.aceEditor); // Needs DOM object
		this.setTheme();
		this.setMode();
		this.switchToAce();
	},

	setTheme: function() {
		this.aceEditor.setTheme('ace/theme/' + GistpenEditor.themeSelect.val());
	},

	setMode: function() {
		var mode = this.languageSelect.val();
		// Nothin is set on init, so default to bash
		if (null === mode) {
			this.languageSelect.val('bash');
			mode = 'bash';
		}
		if('js' === mode) {
			this.aceEditor.getSession().setMode('ace/mode/javascript');
		} else if( 'bash' === mode) {
			this.aceEditor.getSession().setMode('ace/mode/sh');
		} else {
			this.aceEditor.getSession().setMode('ace/mode/' + mode);
		}
	},

	loadClickHandlers: function() {
		var thiseditor = this;

		this.textButton.click(function(){
			thiseditor.switchToText();
		});

		this.aceButton.click(function(){
			thiseditor.switchToAce();
		});

		this.aceEditor.getSession().on('change', function(event) {
			thiseditor.updateTextContent();
		});

		this.deleteFileButton.click(function(event) {
			event.preventDefault();
			thiseditor.deleteEditor();
		});

		GistpenEditor.themeSelect.change(function(event) {
			thiseditor.aceEditor.setTheme('ace/theme/' + GistpenEditor.themeSelect.val());
		});

		this.languageSelect.change(function() {
			thiseditor.setMode();
		});
	},

	switchToText: function() {
		this.aceEditorDiv.hide();
		this.editorTextArea.show();
		this.editorWrap.addClass('html-active').removeClass('ace-active');
	},

	switchToAce: function() {
		this.updateAceContent();
		this.editorTextArea.hide();
		this.aceEditorDiv.show();
		this.editorWrap.addClass('ace-active').removeClass('html-active');
		this.aceEditor.focus();
	},

	deleteEditor: function() {
		var thiseditor = this;

		// Confirm we really want to delete
		var r = confirm("Are you sure you want to delete this Gistpen?");
		if (r === false) {
			return;
		}

		this.editorFull.remove();
		jQuery.post(ajaxurl,{
			action: 'delete_gistpenfile',

			nonce: GistpenEditor.getNonce(),
			fileID: thiseditor.fileID,
		}, function(response) {
			if(response.success === false) {
				console.log('Failed to delete file.');
				console.log(response.data.message);
			} else {
				var currentFileIDs = GistpenEditor.fileIDs.val();
				var newFilesIDs = currentFileIDs.replace(" " + thiseditor.fileID, "");
				GistpenEditor.fileIDs.val(newFilesIDs);
			}
		});
	},

	updateAceContent: function() {
		this.aceEditor.getSession().setValue(this.editorTextArea.val());
	},

	updateTextContent: function() {
		this.editorTextArea.val(this.aceEditor.getValue());
	},

	addID: function() {
		this.filenameInput.attr({
			id: 'wp-gistpenfile-slug-'+this.fileID,
			name: 'wp-gistpenfile-slug-'+this.fileID
		});
		this.languageSelect.attr({
			id: 'wp-gistpenfile-language-'+this.fileID,
			name: 'wp-gistpenfile-language-'+this.fileID
		});
		this.deleteFileButton.attr({
			id: 'wp-delete-gistpenfile-'+this.fileID,
			name: 'wp-delete-gistpenfile-'+this.fileID,
		});
		this.editorTextArea.attr({
			id: 'wp-gistpenfile-code-'+this.fileID,
			name: 'wp-gistpenfile-code-'+this.fileID
		});
		this.aceEditorDiv.attr({
			id: 'ace-editor-'+this.fileID
		});
	},

	addName: function() {
		this.filenameInput.val(this.fileName);
	},

	addContent: function() {
		this.editorTextArea.val(this.fileContent);
	},

	addLanguage: function() {
		this.languageSelect.val(this.fileLanguage);
	}
};
