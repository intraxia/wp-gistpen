<?php
/**
 * @package   WP_Gistpen_editor
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
 * @package WP_Gistpen_Editor
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

		// Call $plugin_slug from public plugin class.
		$plugin = WP_Gistpen::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Add ACE editor
		add_action( 'edit_form_after_title', array( $this, 'add_theme_selection' ) );

		// Load editor style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_editor_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_editor_scripts' ) );

		// Add AJAX hooks for Ace theme
		add_action( 'wp_ajax_gistpen_save_ace_theme', array( $this, 'save_ace_theme' ) );
		add_action( 'wp_ajax_gistpen_get_ace_theme', array( $this, 'get_ace_theme' ) );

		// Add metaboxes
		add_filter( 'cmb_meta_boxes', array( $this, 'add_metaboxes' ) );

		// Disable visual editor
		add_filter( 'user_can_richedit', array( $this, 'disable_visual_editor' ) );

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
	 * Add theme selection field
	 *
	 * @since     0.4.0
	 */
	public function add_theme_selection() {?>
		<select class="cmb_select" name="_wpgp_ace_theme" id="_wpgp_ace_theme">
			<option value="ambiance">Ambiance</option>
			<option value="chaos">Chaos</option>
			<option value="chrome">Chrome</option>
			<option value="clouds">Clouds</option>
			<option value="clouds_midnight">Clouds Midnight</option>
			<option value="cobalt">Cobalt</option>
			<option value="crimson_editor">Crimson Editor</option>
			<option value="dawn">Dawn</option>
			<option value="dreamweaver">Dreamweaver</option>
			<option value="eclipse">Eclipse</option>
			<option value="github">Github</option>
			<option value="idle_fingers">Idle Fingers</option>
			<option value="katzenmilch">Katzenmilch</option>
			<option value="kr">KR</option>
			<option value="kuroir">Kuroir</option>
			<option value="merbivore">Merbivore</option>
			<option value="monokai">Monokai</option>
			<option value="solarized_dark">Solarized Dark</option>
			<option value="solarized_light">Solarized Light</option>
			<option value="twilight">Twilight</option>
		</select>
<?php
	}

	/**
	 * Add the ACE editor styles to the Add Gistpen screen
	 *
	 * @since     0.4.0
	 */
	public function enqueue_editor_styles() {

		$screen = get_current_screen();

		if ('gistpens' == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-editor-styles', WP_GISTPEN_URL . 'admin/assets/css/wp-gistpen-editor.css', array(), WP_Gistpen::VERSION );
		}
	}

	/**
	 * Add the ACE editor scripts to the Add Gistpen screen
	 *
	 * @since     0.4.0
	 */
	public function enqueue_editor_scripts() {

		$screen = get_current_screen();

		if ('gistpens' == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-ace-script', WP_GISTPEN_URL . 'admin/assets/js/ace/ace.js', array(), WP_Gistpen::VERSION, true );
			wp_enqueue_script( $this->plugin_slug . '-editor-script', WP_GISTPEN_URL . 'admin/assets/js/wp-gistpen-editor.min.js', array( 'jquery', $this->plugin_slug . '-ace-script' ), WP_Gistpen::VERSION, true );
		}
	}

	/**
	 * AJAX hook to get ACE editor theme
	 *
	 * @since 0.4.0
	 */
	public function get_ace_theme() {
		if ( !wp_verify_nonce( $_POST['theme_nonce'], 'create_gistpen_ajax' ) ) {
			die( __( "Nonce check failed.", 'wp-gistpen' ) );
		}

		$result = get_option( '_wpgp_ace_theme', 'ambiance' );
		die( $result );
	}

	/**
	 * AJAX hook to save ACE editor theme
	 *
	 * @since     0.4.0
	 */
	public function save_ace_theme() {
		if ( !wp_verify_nonce( $_POST['theme_nonce'], 'create_gistpen_ajax' ) ) {
			die( __( "Nonce check failed.", 'wp-gistpen' ) );
		}

		$result = update_option( '_wpgp_ace_theme', $_POST['theme'] );
		die( $result );
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
		 * Register the description box on the Gistpen
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

		/**
		 * Register the language box on the Gistpen
		 */
		$meta_boxes['gistpen_language'] = array(
			'id'         => 'gistpen_language',
			'title'      => __( 'Gistpen Language', 'wp-gistpen' ),
			'pages'      => array( 'gistpens' ), // Post type
			'context'    => 'side',
			'priority'   => 'high',
			'show_names' => false, // Show field names on the left
			'fields'     => array(
				array(
					'desc'     => 'Select this Gistpen\'s language.',
					'id'       => $prefix . 'gistpen_language',
					'taxonomy' => 'language',
					'type'     => 'taxonomy_select'
				)
			)
		);

		return $meta_boxes;

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

}
