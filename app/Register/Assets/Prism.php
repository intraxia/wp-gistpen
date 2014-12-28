<?php
namespace WP_Gistpen\Register\Assets;

/**
 * Registers the Prism assets of the plugin.
 *
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Prism {

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

		if ( ! defined( 'SCRIPT_DEBUG' ) || SCRIPT_DEBUG !== true ) {
			$this->min = '.min';
		}

	}

	/**
	 * Register the stylesheets for Prism.
	 *
	 * @since    0.5.0
	 */
	public function enqueue_styles() {
		if ( $this->is_prism_required() ) {
			// Add the prism theme css
			$theme = $this->get_theme();
			wp_enqueue_style( $this->plugin_name . '-prism-theme', WP_GISTPEN_URL . 'assets/css/prism/themes/prism' . $theme . '.css', array(), $this->version );

			// Add line highlight css
			wp_enqueue_style( $this->plugin_name . '-prism-line-highlight', WP_GISTPEN_URL . 'assets/css/prism/plugins/line-highlight/prism-line-highlight.css', array( $this->plugin_name . '-prism-theme' ), $this->version );

			// Add line numbers css if needed
			if ( is_admin() ||  'on' === cmb2_get_option( $this->plugin_name, '_wpgp_gistpen_line_numbers' ) ) {
				wp_enqueue_style( $this->plugin_name . '-prism-line-numbers', WP_GISTPEN_URL . 'assets/css/prism/plugins/line-numbers/prism-line-numbers.css', array( $this->plugin_name . '-prism-theme' ), $this->version );
			}
		}
	}

	/**
	 * Checks if Prism is required on current page
	 *
	 * @return boolean Whether to enqueue Prism
	 * @since    0.5.0
	 */
	private function is_prism_required() {
		if ( ! is_admin() || 'settings_page_wp-gistpen' === get_current_screen()->id ) {
			return true;
		}

		return false;
	}

	/**
	 * Retrieve the Prism theme
	 *
	 * @return string Prism theme
	 * @since    0.5.0
	 */
	private function get_theme() {
		$theme = cmb2_get_option( $this->plugin_name, '_wpgp_gistpen_highlighter_theme' );

		if ( '' == $theme || 'default' == $theme ) {
			$theme = '';
		} else {
			$theme = '-' . $theme;
		}

		return $theme;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    0.5.0
	 */
	public function enqueue_scripts() {

		if ( $this->is_prism_required() ) {
			wp_enqueue_script( $this->plugin_name . '-prism', WP_GISTPEN_URL . 'assets/js/prism' . $this->min . '.js', array( 'jquery' ), $this->version, true );
		}

	}

}
