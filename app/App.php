<?php
namespace WP_Gistpen;

use WP_Gistpen\Assets\Dashboard;
use WP_Gistpen\Assets\Prism;
use WP_Gistpen\Assets\TinyMCE;
use WP_Gistpen\Assets\Web;

use WP_Gistpen\Page\Editor;
use WP_Gistpen\Page\Settings;

use WP_Gistpen\Database\Persistance;

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
	 * The classes responsible for plugin functionality.
	 *
	 * @since    0.5.0
	 * @access   public
	 */
	public $ajax;
	public $content;
	public $dashboard;
	public $data;
	public $editor;
	public $migration;
	public $prism;
	public $save;
	public $settings;
	public $tinymce;
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

		$this->register_data();
		$this->register_wp_cli_command();

		$this->define_ajax_hooks();
		$this->define_content_hooks();
		$this->define_dashboard_hooks();
		$this->define_editor_hooks();
		$this->define_migration_hooks();
		$this->define_prism_hooks();
		$this->define_save_hooks();
		$this->define_settings_hooks();
		$this->define_tinymce_hooks();
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

		$plugin_i18n = new Register\I18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the saving functionality
	 * of the plugin.
	 *
	 * @since    0.5.0
	 * @access   private
	 */
	private function register_data() {
		$this->data = new Register\Data( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init', $this->data, 'post_type_gistpen' );
		$this->loader->add_action( 'init', $this->data, 'taxonomy_language' );
		// Register the settings page
		$this->loader->add_shortcode( 'gistpen', $this->data, 'add_shortcode' );
	}

	/**
	 * Register the WP CLI commands for WP-Gistpen.
	 *
	 * @since    0.5.0
	 * @access   private
	 */
	private function register_wp_cli_command() {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			\WP_CLI::add_command( 'wpgp', 'WP_Gistpen\CLI\Command' );
		}
	}

	/**
	 * Register all of the hooks related to the ajax functionality
	 * of the plugin.
	 *
	 * @since    0.5.0
	 * @access   private
	 */
	private function define_ajax_hooks() {

		$this->ajax = new Api\Ajax( $this->get_plugin_name(), $this->get_version() );

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
	 * Register all of the hooks related to the front-end content
	 * of the plugin.
	 *
	 * @since    0.5.0
	 * @access   private
	 */
	private function define_content_hooks() {

		$this->content = new Register\Content( $this->get_plugin_name(), $this->get_version() );

		// Remove some filters from the Gistpen content
		$this->loader->add_action( 'the_content', $this->content, 'remove_filters' );
		// Add the description to the Gistpen content
		$this->loader->add_filter( 'the_content', $this->content, 'post_content' );
		// Remove child posts from the archive page
		$this->loader->add_filter( 'pre_get_posts', $this->content, 'pre_get_posts' );

	}

	/**
	 * Register all of the hooks related to the dashboard assets
	 * of the plugin.
	 *
	 * @since    0.5.0
	 * @access   private
	 */
	private function define_dashboard_hooks() {

		$this->dashboard = new Register\Assets\Dashboard( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $this->dashboard, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $this->dashboard, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the editor views
	 * of the plugin.
	 *
	 * @since    0.5.0
	 * @access   private
	 */
	private function define_editor_hooks() {
		$this->editor = new Register\View\Editor( $this->get_plugin_name(), $this->get_version() );
		// Render the error messages
		$this->loader->add_action( 'admin_notices', $this->editor, 'add_admin_errors' );
		// Edit the placeholder text in the Gistpen title box
		$this->loader->add_filter( 'enter_title_here', $this->editor, 'new_enter_title_here' );
		// Hook in repeatable file editor
		$this->loader->add_action( 'edit_form_after_title', $this->editor, 'render_gistfile_editor' );
		// Init all the rendered editors
		$this->loader->add_action( 'admin_print_footer_scripts', $this->editor, 'add_ace_editor_init_inline', 99 );

		// Rearrange Gistpen layout
		$this->loader->add_filter( 'screen_layout_columns', $this->editor, 'screen_layout_columns' );
		$this->loader->add_action( 'admin_menu', $this->editor, 'remove_meta_boxes' );
		$this->loader->add_filter( 'get_user_option_screen_layout_gistpen', $this->editor, 'screen_layout_gistpen' );
		$this->loader->add_filter( 'get_user_option_meta-box-order_gistpen', $this->editor, 'gistpen_meta_box_order' );

		// Add files column to and reorder Gistpen edit screen
		$this->loader->add_filter( 'manage_gistpen_posts_columns', $this->editor, 'manage_posts_columns' );
		$this->loader->add_action( 'manage_gistpen_posts_custom_column', $this->editor, 'manage_posts_custom_column', 10, 2 );
		$this->loader->add_filter( 'posts_orderby', $this->editor, 'edit_screen_orderby', 10, 2 );
	}

	/**
	 * Register all of the hooks related to the database migrations
	 * for the plugin.
	 *
	 * @since    0.5.0
	 * @access   private
	 */
	public function define_migration_hooks() {
		$this->migration = new Migration( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_init', $this->migration, 'run' );
	}

	/**
	 * Register all of the hooks related to the Prism assets
	 * of the plugin.
	 *
	 * @since    0.5.0
	 * @access   private
	 */
	private function define_prism_hooks() {

		$this->prism = new Register\Assets\Prism( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $this->prism, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $this->prism, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_enqueue_scripts', $this->prism, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $this->prism, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the saving and deleting posts.
	 *
	 * @since    0.5.0
	 * @access   private
	 */
	private function define_save_hooks() {

		$this->save = new Register\Save( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'save_post', $this->save, 'save_post_hook' );
		$this->loader->add_action( 'post_updated', $this->save, 'remove_revision_save', 9 );
		$this->loader->add_action( 'transition_post_status', $this->save, 'sync_post_status', 10, 3 );
		$this->loader->add_action( 'before_delete_post', $this->save, 'delete_post_hook' );

		$this->loader->add_filter( 'wp_save_post_revision_check_for_changes', $this->save, 'disable_check_for_change', 10, 3 );

	}

	/**
	 * Register all of the hooks related to the settings views
	 * of the plugin.
	 *
	 * @since    0.5.0
	 * @access   private
	 */
	private function define_settings_hooks() {
		$this->settings = new Register\View\Settings( $this->get_plugin_name(), $this->get_version() );

		// Add the options page and menu item.
		$this->loader->add_action( 'admin_menu', $this->settings, 'add_plugin_admin_menu' );
		$this->loader->add_action( 'admin_init', $this->settings, 'register_setting' );
		// Add an action link pointing to the options page.
		$this->loader->add_filter( 'plugin_action_links_' . WP_GISTPEN_BASENAME, $this->settings, 'add_action_links' );
	}

	/**
	 * Register all of the hooks related to the TinyMCE assets
	 * of the plugin.
	 *
	 * @since    0.5.0
	 * @access   private
	 */
	private function define_tinymce_hooks() {

		$this->tinymce = new Register\Assets\TinyMCE( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_filter( 'mce_external_plugins', $this->tinymce, 'mce_external_plugins' );
		$this->loader->add_filter( 'mce_buttons', $this->tinymce, 'mce_buttons' );

	}

	/**
	 * Register all of the hooks related to the web assets
	 * of the plugin.
	 *
	 * @since    0.5.0
	 * @access   private
	 */
	private function define_web_hooks() {

		$this->web = new Register\Assets\Web( $this->get_plugin_name(), $this->get_version() );

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
