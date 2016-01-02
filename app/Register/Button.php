<?php
namespace Intraxia\Gistpen\Register;

use Intraxia\Jaxion\Contract\Core\HasFilters;

/**
 * Registers the TinyMCE assets of the plugin.
 *
 * @package    Intraxia\Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 * @todo       Push this class into Jaxion.
 */
class Button implements HasFilters {
	/**
	 * The minification string
	 *
	 * @since    0.5.0
	 * @access   private
	 * @var string
	 */
	protected $min = '.min';

	/**
	 * Plugin url
	 *
	 * @var string
	 */
	protected $url;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.5.0
	 *
	 * @param string $url
	 */
	public function __construct( $url ) {
		$this->url = $url;

		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$this->min = '';
		}
	}

	/**
	 * Adds the TinyMCE plugin
	 *
	 * @param  array $plugins Currently added plugins.
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
	 * @param  array $buttons Currently added buttons.
	 * @return array          Array with newly added button
	 * @since 0.4.0
	 */
	public function mce_buttons( $buttons ) {
		array_push( $buttons, 'wp_gistpen' );
		return $buttons;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return array[]
	 */
	public function filter_hooks() {
		return array(
			array(
				'hook' => 'mce_external_plugins',
				'method' => 'mce_external_plugins',
			),
			array(
				'hook' => 'mce_buttons',
				'method' => 'mce_buttons',
			),
		);
	}
}
