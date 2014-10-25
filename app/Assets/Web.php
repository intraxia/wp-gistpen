<?php
namespace WP_Gistpen\Assets;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Web {

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

		if( ! defined( 'SCRIPT_DEBUG' ) || SCRIPT_DEBUG !== true ) {
			$this->min = '.min';
		}

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    0.5.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name .'-web-styles', WP_GISTPEN_URL . 'assets/css/web' . $this->min . '.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    0.5.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name .'-web-scripts', WP_GISTPEN_URL . 'assets/js/web' . $this->min . '.js', array( 'jquery' ), $this->version, false );

	}

}
