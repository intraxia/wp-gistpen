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
	#wp-content-wrap {
		min-height: 360px;
		width: 100%;
	}
</style>

<script src="<?php echo WP_GISTPEN_URL; ?>public/assets/vendor/ace-builds/src-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
<script>
	var aceeditor;

	jQuery(function() {
		aceeditor = ace.edit('wp-content-wrap');
		aceeditor.setTheme("ace/theme/monokai");
		aceeditor.getSession().setMode("ace/mode/javascript");
		aceeditor.setValue("<?php global $post; echo esc_textarea($post->post_content); ?>");
	});
</script>
