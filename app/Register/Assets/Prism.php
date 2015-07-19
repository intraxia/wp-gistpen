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
	 * The minification string
	 *
	 * @since    0.5.0
	 * @access   private
	 * @var string
	 */
	protected $min = '';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.5.0
	 * @var      string    $plugin_name       The name of the plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct() {
		if ( ! defined( 'SCRIPT_DEBUG' ) || true !== SCRIPT_DEBUG ) {
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
			wp_enqueue_style( \WP_Gistpen::$plugin_name . '-prism-theme', WP_GISTPEN_URL . 'assets/css/prism/themes/prism' . $theme . '.css', array(), \WP_Gistpen::$version );

			// Add line highlight css
			wp_enqueue_style( \WP_Gistpen::$plugin_name . '-prism-line-highlight', WP_GISTPEN_URL . 'assets/css/prism/plugins/line-highlight/prism-line-highlight.css', array( \WP_Gistpen::$plugin_name . '-prism-theme' ), \WP_Gistpen::$version );

			// Add line numbers css if needed
			if ( is_admin() ||  'on' === cmb2_get_option( \WP_Gistpen::$plugin_name, '_wpgp_gistpen_line_numbers' ) ) {
				wp_enqueue_style( \WP_Gistpen::$plugin_name . '-prism-line-numbers', WP_GISTPEN_URL . 'assets/css/prism/plugins/line-numbers/prism-line-numbers.css', array( \WP_Gistpen::$plugin_name . '-prism-theme' ), \WP_Gistpen::$version );
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
		$theme = cmb2_get_option( \WP_Gistpen::$plugin_name, '_wpgp_gistpen_highlighter_theme' );

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
			wp_enqueue_script( \WP_Gistpen::$plugin_name . '-prism', WP_GISTPEN_URL . 'assets/js/prism' . $this->min . '.js', array( 'jquery' ), \WP_Gistpen::$version, true );
		}

	}

}
