<?php
/**
 * @package   WP_Gistpen
 * @author    James DiGioia <jamesorodig@gmail.com>
 * @license   GPL-2.0+
 * @link      http://jamesdigioia.com/wp-gistpen/
 * @copyright 2014 James DiGioia
 */

/**
 * This class manipulates the TinyMCE editor.
 *
 * @package WP_Gistpen_TinyMCE
 * @author  James DiGioia <jamesorodig@gmail.com>
 */
class WP_Gistpen_TinyMCE {

	/**
	 * Adds the TinyMCE plugin
	 *
	 * @param  array $plugins Currently added plugins
	 * @return array          Array with newly added plugin
	 * @since 0.4.0
	 */
	public static function mce_external_plugins( $plugins ) {
		$plugins['wp_gistpen'] = WP_GISTPEN_URL . 'admin/assets/js/tinymce-plugin.min.js';
		return $plugins;
	}

	/**
	 * Adds the TinyMCE button
	 *
	 * @param  array $buttons Currently added buttons
	 * @return array          Array with newly added button
	 * @since 0.4.0
	 */
	public static function mce_buttons( $buttons ) {
		array_push( $buttons, 'wp_gistpen' );
		return $buttons;
	}
}
