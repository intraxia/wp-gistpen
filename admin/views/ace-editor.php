<?php
/**
 * Represents the view for the administration dashboard.
 *
 * @package   WP_Gistpen
 * @author    James DiGioia <jamesorodig@gmail.com>
 * @license   GPL-2.0+
 * @link      http://jamesdigioia.com/wp-gistpen/
 * @copyright 2014 James DiGioia
 */
?>

<style type="text/css" media="screen">
	#ace-editor {
		min-height: 360px;
		width: 100%;
	}
</style>

<script src="<?php echo WP_GISTPEN_URL; ?>public/assets/vendor/ace-builds/src-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
<script>

	jQuery(function() { Ace.init(); });

	var Ace = {

		init: function() {
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
			jQuery('#' + this.aceEditorId).hide();
			jQuery('#wp-content-wrap').addClass('html-active').removeClass('ace-active');
			jQuery('#content').show();
		},

		switchToAce: function() {
			jQuery('#' + this.aceEditorId).show();
			jQuery('#wp-content-wrap').removeClass('html-active').addClass('ace-active');
			jQuery('#content').hide();
		},

		activateAceEditor: function() {
			this.aceEditorDiv.insertAfter('#content');

			// Set up editor on div
			aceEditor = ace.edit(this.aceEditorId);
			aceEditor.setTheme('ace/theme/monokai');
			aceEditor.getSession().setMode('ace/mode/javascript');
			aceEditor.setValue(jQuery('#content').val());

			this.switchToAce();
			jQuery('#wp-content-media-buttons, #ed_toolbar').hide();
		}
	};
</script>
