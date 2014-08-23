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
		add_action( 'before_wp_tiny_mce', array( $this, 'embed_nonce' ) );

		// Add AJAX hook for button clicks
		add_action( 'wp_ajax_gistpen_insert_dialog', array( $this, 'insert_gistpen_dialog' ) );
		add_action( 'wp_ajax_create_gistpen_ajax', array( $this, 'create_gistpen_ajax' ) );
		add_action( 'wp_ajax_search_gistpen_ajax', array( $this, 'search_gistpen_ajax' ) );

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

	/**
	 * Embed the nonce in the head of the editor
	 *
	 * @return string    AJAX nonce
	 * @since  0.2.0
	 */
	public function embed_nonce() {

		wp_nonce_field( 'create_gistpen_ajax', '_ajax_wp_gistpen', false );

	}

	/**
	 * Dialog for adding shortcode
	 *
	 * @return  string   HTML for shortcode dialog
	 * @since   0.2.0
	 */
	public function insert_gistpen_dialog() {

		die(include WP_GISTPEN_DIR . 'admin/views/insert-gistpen.php');

	}

	/**
	 * Responds to AJAX request to search Gistpens
	 *
	 * @return  string   HTML for found gistpens
	 * @since 0.2.0
	 */
	public function search_gistpen_ajax() {
		if ( !wp_verify_nonce( $_POST['gistpen_nonce'], 'create_gistpen_ajax' ) ) {
			die( __( "Nonce check failed.", 'wp-gistpen' ) );
		}

		$args = array(

			'post_type'      => 'gistpens',
			'post_status'    => 'publish',
			'order'          => 'DESC',
			'orderby'        => 'date',
			'posts_per_page' => 5,

		);

		if( isset( $_POST['gistpen_search_term'] ) ) {
			$args['s'] = $_POST['gistpen_search_term'];
		}

		$recent_gistpen_query = new WP_Query( $args );

		$output = '';
		if ( $recent_gistpen_query->have_posts() ) {
			while ( $recent_gistpen_query->have_posts() ) {
				$recent_gistpen_query->the_post();

				$output .= '<li>';
					$output .= '<div class="gistpen-radio"><input type="radio" name="gistpen_id" value="' . get_the_ID() . '"></div>';
					$output .= '<div class="gistpen-title">' . get_the_title() . '</div>';
				$output .= '</li>';

			}
		} else {
			$output .= '<li>';
				$output .= 'No Gistpens found.';
			$output .= '</li>';
		}

		die($output);
	}

	/**
	 * Responds to AJAX request to create new Gistpen
	 *
	 * @return string $post_id the id of the created Gistpen
	 * @since  0.2.0
	 */
	public function create_gistpen_ajax() {

		if ( !wp_verify_nonce( $_POST['gistpen_nonce'], 'create_gistpen_ajax' ) ) {
			die( __( "Nonce check failed.", 'wp-gistpen' ) );
		}

		$args = array(
			'post_title'   => $_POST['gistpen_title'],
			'post_content' => $_POST['gistpen_content'],
			'post_type'    => 'gistpens',
			'post_status'  => 'publish',
			'tax_input'    => array(
				'language'   => $_POST['gistpen_language'],
			),
		);
		$post_id = wp_insert_post( $args, false );

		if( $post_id === 0 ) {
			die( "Failed to insert post. ");
		}

		if( $_POST['gistpen_description'] !== "" ) {
			update_post_meta( $post_id, '_wpgp_gistpen_description', $_POST['gistpen_description'] );
		}

		die(print($post_id));

	}

}
