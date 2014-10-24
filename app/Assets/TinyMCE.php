<?php
namespace WP_Gistpen\Assets;

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
class TinyMCE {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.5.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.5.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The minification string
	 *
	 * @since    0.5.0
	 * @access   private
	 * @var string
	 */
	private $min = '';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.5.0
	 * @var      string    $plugin_name       The name of the plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		if( ! defined( 'SCRIPT_DEBUG' ) || 'SCRIPT_DEBUG' !== true ) {
			$this->min = '.min';
		}

	}

	/**
	 * Adds the TinyMCE plugin
	 *
	 * @param  array $plugins Currently added plugins
	 * @return array          Array with newly added plugin
	 * @since 0.4.0
	 */
	public function mce_external_plugins( $plugins ) {
		$plugins['wp_gistpen'] = WP_GISTPEN_URL . 'assets/js/tinymce' . $this->min . '.js';
		return $plugins;
	}

	/**
	 * Adds the TinyMCE button
	 *
	 * @param  array $buttons Currently added buttons
	 * @return array          Array with newly added button
	 * @since 0.4.0
	 */
	public function mce_buttons( $buttons ) {
		array_push( $buttons, 'wp_gistpen' );
		return $buttons;
	}
}
