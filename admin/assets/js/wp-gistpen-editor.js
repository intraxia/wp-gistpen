jQuery(function() { GistpenAce.init(); });

var GistpenAce = {

	init: function() {
		window.gistpenAce = this;
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
		this.aceEditorDiv.insertAfter('#content');

		// Set up editor on div
		this.aceEditor = ace.edit(this.aceEditorId);
		this.setUpThemeAndMode();

		jQuery('#insert-media-button, #ed_toolbar, input[name="submit-cmb"]').hide();
		this.themeSelect.parents('table.form-table.cmb_metabox').hide();
		this.themeSelect.appendTo('#wp-content-media-buttons');
		this.aceEditor.getSession().on('change', function(event) {
			window.gistpenAce.updateTextContent();
		});
		this.switchToAce();
	},

	setUpThemeAndMode: function() {
		// Set theme and enable listener
		jQuery.post(ajaxurl,{
			action: 'gistpen_get_ace_theme',
			theme_nonce: jQuery.trim(jQuery('#_ajax_wp_gistpen').val()),
			}, function(response) {
				window.gistpenAce.themeSelect.val(response);
				window.gistpenAce.aceEditor.setTheme('ace/theme/' + response);
		});
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
