<?php
/**
 * @package   WP_Gistpen
 * @author    James DiGioia <jamesorodig@gmail.com>
 * @license   GPL-2.0+
 * @link      http://jamesdigioia.com/wp-gistpen/
 * @copyright 2014 James DiGioia
 */

/**
 * Plugin class. This class manipulates the
 * editor for the custom post type and
 * the TinyMCE editor.
 *
 * @package WP_Gistpen_Post_Editor
 * @author  James DiGioia <jamesorodig@gmail.com>
 */
class WP_Gistpen_Post_Editor {

	/**
	 * Instance of this class.
	 *
	 * @since    0.2.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the editor enhancements by loading the metaboxes
	 * and the new TinyMCE button.
	 *
	 * @since     0.2.0
	 */
	private function __construct() {

		// Call $plugin_slug from public plugin class.
		$plugin = WP_Gistpen::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Add TinyMCE Editor Buttons
		add_filter( 'mce_external_plugins', array( $this, 'add_button' ) );
		add_filter( 'mce_buttons', array( $this, 'register_button' ) );
		add_action( 'before_wp_tiny_mce', array( 'WP_Gistpen_AJAX', 'embed_nonce' ) );

		// Add AJAX hook for button clicks
		add_action( 'wp_ajax_gistpen_insert_dialog', array( 'WP_Gistpen_AJAX', 'insert_gistpen_dialog' ) );
		add_action( 'wp_ajax_create_gistpen_ajax', array( 'WP_Gistpen_AJAX', 'create_gistpen_ajax' ) );
		add_action( 'wp_ajax_search_gistpen_ajax', array( 'WP_Gistpen_AJAX', 'search_gistpen_ajax' ) );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @return    object    A single instance of this class.
	 * @since     0.2.0
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}

	/**
	 * Register the script for the button with TinyMCE
	 *
	 * @param  array    $plugins    array of current plugins
	 * @return array                updated array with new button
	 */
	public function add_button( $plugins ) {

		$plugins['wp_gistpen'] = WP_GISTPEN_URL . 'admin/assets/js/wp-gistpen-tinymce-plugin.min.js';
		return $plugins;

	}

	/**
	 * Add WP-Gistpen's editor button to the editor
	 *
	 * @param  array    $buttons   array of current buttons
	 * @return array               updated array with new button
	 * @since    0.2.0
	 */
	public function register_button( $buttons ) {

		array_push( $buttons, 'wp_gistpen' );
		return $buttons;

	}

}
