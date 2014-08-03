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

		// Call $plugin_slug from public plugin class.
		$plugin = WP_Gistpen::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Run the updater
		add_action( 'admin_init', array( $this, 'init' ) );

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
	 * Functions run on the init hook
	 *
	 * @since     0.3.0
	 */
	public function init() {

		$this->run_updater();
		$this->register_setting();
	}

	/**
	 * Checks if we're behind current version
	 * and triggers the updater
	 *
	 * @since 0.3.0
	 */
	public function run_updater() {

		// Check if plugin needs to be upgraded
		$version = get_option( 'wp_gistpen_version' );

		if( $version !== WP_Gistpen::VERSION ) {
			WP_Gistpen_Updater::update( $version );
			update_option( 'wp_gistpen_version', WP_Gistpen::VERSION );
		}

	}

	/**
	 * Register the settings (obviously)
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
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/wp-gistpen-admin.min.js', __FILE__ ), array( 'jquery', $this->plugin_slug . '-plugin-script' ), WP_Gistpen::VERSION, true );
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
