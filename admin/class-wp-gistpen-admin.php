<?php
/**
 * @package   WP_Gistpen_Admin
 * @author    James DiGioia <jamesorodig@gmail.com>
 * @license   GPL-2.0+
 * @link      http://jamesdigioia.com/wp-gistpen/
 * @copyright 2014 James DiGioia
 */

/**
 * Plugin class. This class works with the
 * admin-facing side of the WordPress site.
 *
 * @package WP_Gistpen_Admin
 * @author  James DiGioia <jamesorodig@gmail.com>
 */
class WP_Gistpen_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    0.1.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    0.1.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = 'wp-gistpen';

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     0.1.0
	 */
	private function __construct() {
		// Require helper classes
		// @todo better autoloading?
		require_once( WP_GISTPEN_DIR . 'admin/includes/class-wp-gistpen-updater.php' );
		require_once( WP_GISTPEN_DIR . 'admin/includes/class-wp-gistpen-ajax.php' );
		require_once( WP_GISTPEN_DIR . 'admin/includes/class-wp-gistpen-editor.php' );
		require_once( WP_GISTPEN_DIR . 'admin/includes/class-wp-gistpen-saver.php' );
		require_once( WP_GISTPEN_DIR . 'admin/includes/class-wp-gistpen-tinymce.php' );

		// Call $plugin_slug from public plugin class.
		$plugin = WP_Gistpen::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Register the settings page
		add_action( 'admin_init', array( $this, 'register_setting' ) );

		// Run the updater
		add_action( 'admin_init', array( 'WP_Gistpen_Updater', 'run' ) );

		/**
		 * TinyMCE hooks
		 */
		// Add TinyMCE Editor Buttons
		add_filter( 'mce_external_plugins', array( 'WP_Gistpen_TinyMCE', 'mce_external_plugins' ) );
		add_filter( 'mce_buttons', array( 'WP_Gistpen_TinyMCE', 'mce_buttons' ) );

		/**
		 * Gistpen Editor hooks
		 */
		// Render the error messages
		add_action( 'admin_notices', array( 'WP_Gistpen_Editor', 'add_admin_errors' ) );
		// Edit the placeholder text in the Gistpen title box
		add_filter( 'enter_title_here', array( 'WP_Gistpen_Editor', 'new_enter_title_here' ) );
		// Hook in repeatable file editor
		add_action( 'edit_form_after_title', array( 'WP_Gistpen_Editor', 'render_gistfile_editor' ) );
		// Load Gistpen editor stylesheet and scripts
		add_action( 'admin_enqueue_scripts', array( 'WP_Gistpen_Editor', 'enqueue_editor_styles' ) );
		add_action( 'admin_enqueue_scripts', array( 'WP_Gistpen_Editor', 'enqueue_editor_scripts' ) );
		// Init all the rendered editors
		add_action( 'admin_print_footer_scripts', array( 'WP_Gistpen_Editor', 'add_ace_editor_init_inline' ), 99 );

		// Rearrange Gistpen layout
		add_filter( 'screen_layout_columns', array( 'WP_Gistpen_Editor', 'screen_layout_columns' ) );
		add_action( 'admin_menu', array( 'WP_Gistpen_Editor', 'remove_meta_boxes' ) );
		add_filter( 'get_user_option_screen_layout_gistpen', array( 'WP_Gistpen_Editor', 'screen_layout_gistpen' ) );
		add_filter( 'get_user_option_meta-box-order_gistpen', array( 'WP_Gistpen_Editor', 'gistpen_meta_box_order') );

		// Add files column to and reorder Gistpen edit screen
		add_filter( 'manage_gistpen_posts_columns', array( 'WP_Gistpen_Editor', 'manage_posts_columns' ) );
		add_action( 'manage_gistpen_posts_custom_column', array( 'WP_Gistpen_Editor', 'manage_posts_custom_column' ), 10, 2 );
		add_filter( 'posts_orderby', array( 'WP_Gistpen_Editor', 'edit_screen_orderby' ), 10, 2 );


		/**
		 * Gistpen save hook
		 */
		// Save the files and attach to Gistpen
		add_action( 'save_post_gistpen', array( 'WP_Gistpen_Saver', 'save_gistpen' ) );

		/**
		 * AJAX hooks
		 */
		// Embed the nonce
		add_action( 'edit_form_after_title', array( 'WP_Gistpen_AJAX', 'embed_nonce' ) );

		// AJAX hook for TinyMCE button
		add_action( 'wp_ajax_get_gistpens', array( 'WP_Gistpen_AJAX', 'get_gistpens' ) );
		add_action( 'wp_ajax_get_gistpen_languages', array( 'WP_Gistpen_AJAX', 'get_gistpen_languages' ) );
		add_action( 'wp_ajax_create_gistpen', array( 'WP_Gistpen_AJAX', 'create_gistpen' ) );

		// AJAX hook to save Ace theme
		add_action( 'wp_ajax_save_ace_theme', array( 'WP_Gistpen_AJAX', 'save_ace_theme' ) );

		// AJAX hooks to add and delete Gistfile editors
		add_action( 'wp_ajax_get_gistpenfile_id', array( 'WP_Gistpen_AJAX', 'get_gistpenfile_id' ) );
		add_action( 'wp_ajax_delete_gistpenfile', array( 'WP_Gistpen_AJAX', 'delete_gistpenfile' ) );

		/**
		 * Options page hooks
		 */
		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'localize_script' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @return    object    A single instance of this class.
	 * @since     0.1.0
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}

	/**
	 * Register the settings page (obviously)
	 *
	 * @since 0.3.0
	 */
	public function register_setting() {
		register_setting( $this->plugin_slug, $this->plugin_slug );
	}

	/**
	 * Register and enqueue admin-specific styles.
	 *
	 * @return    null    Return early if no settings page is registered.
	 * @since     0.1.0
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		wp_enqueue_style( $this->plugin_slug .'-admin-styles', WP_GISTPEN_URL . 'admin/assets/css/wp-gistpen-admin.css', array(), WP_Gistpen::VERSION );

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @return    null    Return early if no settings page is registered.
	 * @since     0.1.0
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', WP_GISTPEN_URL . 'admin/assets/js/wp-gistpen-admin.min.js', array( 'jquery', $this->plugin_slug . '-plugin-script' ), WP_Gistpen::VERSION, true );
			$instance = WP_Gistpen::get_instance();
			$instance->enqueue_styles();
			$instance->enqueue_scripts();
		}

	}

	/**
	 * Localize the admin script
	 *
	 * @since    0.3.0
	 */
	public function localize_script() {
		wp_localize_script( $this->plugin_slug .'-admin-script', 'WP_GISTPEN_URL', WP_GISTPEN_URL );
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    0.1.0
	 */
	public function add_plugin_admin_menu() {

		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'WP-Gistpen Settings', $this->plugin_slug ),
			__( 'Gistpens', $this->plugin_slug ),
			'edit_posts',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    0.1.0
	 */
	public function display_plugin_admin_page() {

		include_once( WP_GISTPEN_DIR . 'admin/views/settings-page.php' );

	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    0.1.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}

}
