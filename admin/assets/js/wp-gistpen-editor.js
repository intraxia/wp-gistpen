jQuery(function() { Ace.init(); });

var Ace = {

	init: function() {
		var self = this;
		this.aceEditorId = 'ace-editor';
		this.aceEditorDiv = jQuery('<div id="' + this.aceEditorId + '"></div>').css({
			left: 0,
			top: 0,
			bottom: 0,
			right: 0,
			zIndex: 1
		});
		this.textButton = jQuery('<a id="content-html" class="hide-if-no-js wp-switch-editor switch-html">Text</a>');
		this.aceButton = jQuery('<a id="content-ace" class="hide-if-no-js wp-switch-editor switch-ace">Ace</a>');
		this.contentWrapDiv = jQuery('#wp-content-wrap');
		this.contentDiv = jQuery('#content');
		this.themeSelect = jQuery('#_wpgp_ace_theme');
		this.languageSelect = jQuery('#_wpgp_gistpen_language');

		this.addSwitchButtons();
		this.loadClickHandlers();
		this.activateAceEditor();
	},

	addSwitchButtons: function() {
		this.textButton.appendTo('#wp-content-editor-tools');
		this.aceButton.appendTo('#wp-content-editor-tools');
	},

	loadClickHandlers: function() {
		this.textButton.click(function() {
			Ace.switchToText();
		}
		);
		this.aceButton.click(function() {
			Ace.switchToAce();
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

	activateAceEditor: function() {
		this.aceEditorDiv.insertAfter('#content');

		// Set up editor on div
		this.aceEditor = ace.edit(this.aceEditorId);
		this.setUpThemeAndMode();

		jQuery('#insert-media-button, #ed_toolbar, input[name="submit-cmb"]').hide();
		this.themeSelect.parents('table.form-table.cmb_metabox').hide();
		this.themeSelect.appendTo('#wp-content-media-buttons');
		this.aceEditor.getSession().on('change', function(event) {
			self.updateTextContent();
		});
		this.switchToAce();
	},

	setUpThemeAndMode: function() {
		// Set theme and enabl listener
		this.aceEditor.setTheme('ace/theme/' + this.themeSelect.val());
		this.themeSelect.change(function() {
			self.aceEditor.setTheme('ace/theme/' + self.themeSelect.val());
			jQuery.post(ajaxurl, {
				action: 'gistpen_save_ace_theme',

				theme_nonce: jQuery.trim(jQuery('#_ajax_wp_gistpen').val()),
				theme: self.themeSelect.val(),

			}, function(response) {
				if(response === false) {
					console.log('Failed to save ACE theme.');
				}
			});
		});
		// Set mode and enable listener
		this.setMode(this.languageSelect.val());
		this.languageSelect.change(function() {
			self.setMode(self.languageSelect.val());
		});
	},

	setMode: function(mode) {
		if('js' === mode) {
			this.aceEditor.getSession().setMode('ace/mode/javascript');
		} else if( 'bash' === mode) {
			this.aceEditor.getSession().setMode('ace/mode/nix');
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
