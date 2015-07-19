<?php
namespace WP_Gistpen\Register\Assets;

/**
 * Registers the web assets of the plugin.
 *
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Web {

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
	public function __construct() {
		if ( ! defined( 'SCRIPT_DEBUG' ) || true !== SCRIPT_DEBUG ) {
			$this->min = '.min';
		}
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    0.5.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( \WP_Gistpen::$plugin_name .'-web-styles', WP_GISTPEN_URL . 'assets/css/web' . $this->min . '.css', array(), \WP_Gistpen::$version, 'all' );
	}

}
