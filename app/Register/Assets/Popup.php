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
	public function __construct() {
		if ( ! defined( 'SCRIPT_DEBUG' ) || true !== SCRIPT_DEBUG ) {
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
			wp_enqueue_style( \WP_Gistpen::$plugin_name .'-popup-styles', WP_GISTPEN_URL . 'assets/css/popup' . $this->min . '.css', array(), \WP_Gistpen::$version );
		}
	}

	/**
	 * Register the JavaScript for the Dashboard.
	 *
	 * @since    0.5.0
	 */
	public function enqueue_scripts() {
		if ( 'post' === get_current_screen()->id || 'page' === get_current_screen()->id ) {
			wp_enqueue_script( \WP_Gistpen::$plugin_name . '-ace-script', WP_GISTPEN_URL . 'assets/js/ace/ace.js', array(), \WP_Gistpen::$version, false );
			wp_enqueue_script( \WP_Gistpen::$plugin_name . '-popup-script', WP_GISTPEN_URL . 'assets/js/popup' . $this->min . '.js', array( 'jquery', \WP_Gistpen::$plugin_name . '-ace-script' ), \WP_Gistpen::$version, false );
			wp_localize_script( \WP_Gistpen::$plugin_name . '-popup-script', 'gistpenLanguages', Language::$supported );
		}

	}

}
