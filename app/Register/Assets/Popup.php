<?php
namespace WP_Gistpen\Register\Assets;

use WP_Gistpen\Model\Language;

/**
 * Registers the web assets of the plugin.
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Popup {

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

		if ( ! defined( 'SCRIPT_DEBUG' ) || SCRIPT_DEBUG !== true ) {
			$this->min = '.min';
		}

	}

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @since    0.5.0
	 */
	public function enqueue_styles() {
		if ( 'post' === get_current_screen()->id || 'page' === get_current_screen()->id ) {
			wp_enqueue_style( $this->plugin_name .'-popup-styles', WP_GISTPEN_URL . 'assets/css/popup' . $this->min . '.css', array(), $this->version );
		}
	}

	/**
	 * Register the JavaScript for the Dashboard.
	 *
	 * @since    0.5.0
	 */
	public function enqueue_scripts() {
		if ( 'post' === get_current_screen()->id || 'page' === get_current_screen()->id ) {
			wp_enqueue_script( $this->plugin_name . '-ace-script', WP_GISTPEN_URL . 'assets/js/ace/ace.js', array(), $this->version, false );
			wp_enqueue_script( $this->plugin_name . '-popup-script', WP_GISTPEN_URL . 'assets/js/popup' . $this->min . '.js', array( 'jquery', $this->plugin_name . '-ace-script' ), $this->version, false );
			wp_localize_script( $this->plugin_name . '-popup-script', 'gistpenLanguages', Language::$supported );
		}

	}

}
