jQuery(function() { GistpenAce.init(); });

var GistpenAce = {

	init: function() {
		window.gistpenAce = this;
		this.aceEditorId = 'ace-editor';
		this.aceEditorDiv = jQuery('#' + this.aceEditorId);
		this.textButton = jQuery('#content-html');
		this.aceButton = jQuery('#content-ace');
		this.contentWrapDiv = jQuery('#wp-gistfile-content-new-wrap');
		this.contentDiv = jQuery('#gistfile-content-new');
		this.themeSelect = jQuery('#_wpgp_ace_theme');
		this.languageSelect = jQuery('#gistfile-language-new');

		jQuery('#titlediv .inside').remove();

		this.loadClickHandlers();
		this.activateAceEditor();
	},

	loadClickHandlers: function() {
		this.textButton.click(function() {
			window.gistpenAce.switchToText();
		});
		this.aceButton.click(function() {
			window.gistpenAce.switchToAce();
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
		// Set up editor on div
		this.aceEditor = ace.edit(this.aceEditorId);
		this.setUpThemeAndMode();

		this.aceEditor.getSession().on('change', function(event) {
			window.gistpenAce.updateTextContent();
		});
		this.switchToAce();
	},

	setUpThemeAndMode: function() {
		this.aceEditor.setTheme('ace/theme/' + this.themeSelect.val());
		this.themeSelect.change(function() {
			window.gistpenAce.aceEditor.setTheme('ace/theme/' + window.gistpenAce.themeSelect.val());
			jQuery.post(ajaxurl, {
				action: 'gistpen_save_ace_theme',

				theme_nonce: jQuery.trim(jQuery('#_ajax_wp_gistpen').val()),
				theme: window.gistpenAce.themeSelect.val(),

			}, function(response) {
				if(response === false) {
					console.log('Failed to save ACE theme.');
				}
			});
		});
		// Set mode and enable listener
		this.setMode(this.languageSelect.val());
		this.languageSelect.change(function() {
			window.gistpenAce.setMode(window.gistpenAce.languageSelect.val());
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
