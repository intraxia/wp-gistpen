<?php
namespace WP_Gistpen\Register;

/**
 * Registers the TinyMCE assets of the plugin.
 *
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Button {

	public $filters = array(
		array(
			'hook' => 'mce_external_plugins',
			'method' => 'mce_external_plugins',
		),
		array(
			'hook' => 'mce_buttons',
			'method' => 'mce_buttons',
		),
	);
	/**
	 * The minification string
	 *
	 * @since    0.5.0
	 * @access   private
	 * @var string
	 */
	protected $min = '.min';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.5.0
	 * @var      string    $plugin_name       The name of the plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $url ) {
		$this->url = $url; // @todo move this class into framework

		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$this->min = '';
		}
	}

	/**
	 * Adds the TinyMCE plugin
	 *
	 * @param  array $plugins Currently added plugins
	 * @return array          Array with newly added plugin
	 * @since 0.4.0
	 */
	public function mce_external_plugins( $plugins ) {
		$plugins['wp_gistpen'] = $this->url . 'assets/js/button' . $this->min . '.js';
		return $plugins;
	}

	/**
	 * Adds the TinyMCE button
	 *
	 * @param  array $buttons Currently added buttons
	 * @return array          Array with newly added button
	 * @since 0.4.0
	 */
	public function mce_buttons( $buttons ) {
		array_push( $buttons, 'wp_gistpen' );
		return $buttons;
	}
}
