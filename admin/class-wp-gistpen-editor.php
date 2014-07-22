<?php
/**
 * @package   WP_Gistpen_editor
 * @author    James DiGioia <jamesorodig@gmail.com>
 * @license   GPL-2.0+
 * @link      http://jamesdigioia.com/wp-gistpen/
 * @copyright 2014 James DiGioia
 */

/**
 * Plugin class. This class manipulated the
 * editor for the custom post type and
 * the TinyMCE editor.
 *
 * @package WP_Gistpen_editor
 * @author  James DiGioia <jamesorodig@gmail.com>
 */
class WP_Gistpen_Editor {

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

		// Add metaboxes
		add_action( 'init', array( $this, 'initialize_meta_boxes' ), 9999 );
		add_filter( 'cmb_meta_boxes', array( $this, 'add_metaboxes' ) );

		// Disable visual editor
		add_filter( 'user_can_richedit', array( $this, 'disable_visual_editor' ) );

		// Add TinyMCE Editor Buttons
		add_filter( 'mce_external_plugins', array( $this, 'add_button' ) );
		add_filter( 'mce_buttons', array( $this, 'register_button' ) );

		// Add AJAX hook for button click
		add_action( 'wp_ajax_gistpen_insert', array( $this, 'insert_gistpen_dialog' ) );
		add_action( 'wp_ajax_nopriv_gistpen_insert', array( $this, 'insert_gistpen_dialog' ) );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     0.2.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}

	/**
	 * Disable the visual editor because
	 * it messes with the code layout
	 *
	 * @return   false|$default     disables only on gistpens
	 * @since    0.2.0
	 */
	public function disable_visual_editor( $default ) {
		global $post;

		if ( 'gistpens' == get_post_type( $post ) )
			return false;
		return $default;
	}

	/**
	 * Initialize the metabox class.
	 *
	 * @since    0.2.0
	 */
	public function initialize_meta_boxes() {

		if ( ! class_exists( 'cmb_Meta_Box' ) )
			require_once( WP_GISTPEN_DIR . 'includes/webdevstudios/custom-metaboxes-and-fields-for-wordpress/init.php' );

	}

	/**
	 * Register the metaboxes
	 *
	 * @since    0.2.0
	 */
	public function add_metaboxes() {

		// Start with an underscore to hide fields from custom fields list
		$prefix = '_wpgp_';

		/**
		 * Sample metabox to demonstrate each field type included
		 */
		$meta_boxes['gistpen_description'] = array(
			'id'         => 'gistpen_description',
			'title'      => __( 'Gistpen Description', 'wp-gistpen' ),
			'pages'      => array( 'gistpens' ), // Post type
			'context'    => 'normal',
			'priority'   => 'high',
			'show_names' => false, // Show field names on the left
			'fields'     => array(
				array(
					'desc'       => __( 'Write a short description of this Gistpen.', 'wp-gistpen' ),
					'id'         => $prefix . 'gistpen_description',
					'type'       => 'textarea',
					// 'show_on_cb' => 'cmb_test_text_show_on_cb', // function should return a bool value
					// 'sanitization_cb' => 'my_custom_sanitization', // custom sanitization callback parameter
					// 'escape_cb'       => 'my_custom_escaping',  // custom escaping callback parameter
					'on_front'        => false, // Optionally designate a field to wp-admin only
				),
			)
		);

		return $meta_boxes;

	}

	/**
	 * Add WP-Gistpen's editor button to the editor
	 *
	 * @param  array    $plugins    array of current plugins
	 * @return array                updated array with new button
	 */
	public function add_button( $plugins ) {

		$plugins['wp_gistpen'] = WP_GISTPEN_URL . 'admin/assets/js/wp-gistpen-editor.min.js';
		return $plugins;

	}

	/**
	 * Register the script for the button with TinyMCE
	 *
	 * @param  array    $buttons   array of current buttons
	 * @return array               updated array with new button
	 * @since    0.2.0
	 */
	public function register_button( $buttons ) {

		array_push($buttons, 'wp_gistpen');
		return $buttons;

	}

	/**
	 * Dialog for adding shortcode
	 *
	 * @since 3.1.0
	 */
	public function insert_gistpen_dialog() {

		die(require_once WP_GISTPEN_DIR . 'admin/assets/views/insert-gistpen.php');
	}

}
