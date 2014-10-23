<?php
namespace WP_Gistpen;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class App {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    0.5.0
	 * @access   protected
	 * @var      Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The class respsponsible for the dashboard-specific functionality of the plugin.
	 *
	 * @since    0.5.0
	 * @access   public
	 * @var      Dashboard    $admin    Controls all the admin functionality for the plugin.
	 */
	public $dashboard;

	/**
	 * The class respsponsible for the web-facing functionality of the plugin.
	 *
	 * @since    0.5.0
	 * @access   public
	 * @var      Web    $public    Controls all the public functionality for the plugin.
	 */
	public $web;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.5.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.5.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 * @since    0.5.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->loader = new Loader();
		$this->set_locale();
		$this->define_ajax_hooks();
		$this->define_dashboard_hooks();
		$this->define_web_hooks();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    0.5.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new I18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the ajax functionality
	 * of the plugin.
	 *
	 * @since    0.5.0
	 * @access   private
	 */
	private function define_ajax_hooks() {

		$this->ajax = new Ajax( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'edit_form_after_title', $this->ajax, 'embed_nonce' );

		// AJAX hook for TinyMCE button
		$this->loader->add_action( 'wp_ajax_get_gistpens', $this->ajax, 'get_gistpens' );
		$this->loader->add_action( 'wp_ajax_get_gistpen_languages', $this->ajax, 'get_gistpen_languages' );
		$this->loader->add_action( 'wp_ajax_create_gistpen', $this->ajax, 'create_gistpen' );

		// AJAX hook to save Ace theme
		$this->loader->add_action( 'wp_ajax_save_ace_theme', $this->ajax, 'save_ace_theme' );

		// AJAX hooks to add and delete Gistfile editors
		$this->loader->add_action( 'wp_ajax_get_gistpenfile_id', $this->ajax, 'get_gistpenfile_id' );
		$this->loader->add_action( 'wp_ajax_delete_gistpenfile', $this->ajax, 'delete_gistpenfile' );

	}

	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 *
	 * @since    0.5.0
	 * @access   private
	 */
	private function define_dashboard_hooks() {

		$this->dashboard = new Dashboard( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $this->dashboard, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $this->dashboard, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $this->dashboard, 'add_plugin_admin_menu' );

	}

	/**
	 * Register all of the hooks related to the web functionality
	 * of the plugin.
	 *
	 * @since    0.5.0
	 * @access   private
	 */
	private function define_web_hooks() {

		$this->web = new Web( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $this->web, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $this->web, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    0.5.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     0.5.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     0.5.0
	 * @return    Plugin_Name_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     0.5.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
