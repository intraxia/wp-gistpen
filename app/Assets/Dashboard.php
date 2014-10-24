<?php
namespace WP_Gistpen\Assets;

use WP_Gistpen\Database\Query;

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, a settings page, and two examples hooks
 * for how to enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Dashboard {

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
	 * @var      string    $plugin_name       The name of this plugin.
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
	 * Register the stylesheets for the Dashboard.
	 *
	 * @since    0.5.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, WP_GISTPEN_URL . 'assets/css/dashboard' . $this->min . '.css', array(), $this->version, 'all' );

		if ( get_current_screen()->id === 'gistpen' ) {
				wp_enqueue_style( $this->plugin_name .'-editor-styles', WP_GISTPEN_URL . 'assets/css/editor.css', array(), WP_Gistpen::VERSION );
			}

	}

	/**
	 * Register the JavaScript for the Dashboard.
	 *
	 * @since    0.5.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, WP_GISTPEN_URL . 'assets/js/dashboard' . $this->min . '.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( $this->plugin_name . '-ace-script', WP_GISTPEN_URL . 'assets/js/ace/ace.js', array(), WP_Gistpen::VERSION, false );
		wp_enqueue_script( $this->plugin_name . '-editor-script', WP_GISTPEN_URL . 'assets/js/editor.min.js', array( 'jquery', $this->plugin_name . '-ace-script' ), WP_Gistpen::VERSION, false );

		wp_localize_script( $this->plugin_name . '-editor-script', 'gistpenLanguages', Query::get_languages() );

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    0.5.0
	 */
	public function add_plugin_admin_menu() {

		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Plugin Name Settings', $this->plugin_name ),
			__( 'Plugin Name', $this->plugin_name ),
			'edit_posts',
			$this->plugin_name,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    0.5.0
	 */
	public function display_plugin_admin_page() {

		include_once( WP_GISTPEN_DIR . 'partials/settings-page.php' );

	}

}
