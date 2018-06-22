<?php
namespace Intraxia\Gistpen\View;

use Intraxia\Gistpen\Contract\Templating;
use Intraxia\Gistpen\Params\Repository as Params;
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
	 * Template service.
	 *
	 * @var Templating
	 */
	private $tmpl;

	/**
	 * Params service.
	 *
	 * @var Params
	 */
	private $params;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.5.0
	 *
	 * @param Templating $tmpl
	 * @param Params     $params
	 * @param string     $url
	 */
	public function __construct( Templating $tmpl, Params $params, $url ) {
		$this->tmpl = $tmpl;
		$this->params = $params;
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
		$plugins['wp_gistpen'] = $this->url . 'assets/js/tinymce' . $this->min . '.js';
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
	 * Output the default state used by the TinyMCE plugin in a script tag.
	 */
	public function output_tinymce_state() {
		echo $this->tmpl->render( 'tinymce', $this->params->state( 'button' ) );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return array[]
	 */
	public function filter_hooks() {
		$hooks = array(
			array(
				'hook' => 'mce_external_plugins',
				'method' => 'mce_external_plugins',
			),
			array(
				'hook' => 'mce_buttons',
				'method' => 'mce_buttons',
			),
		);

		foreach ( array('post.php','post-new.php') as $hook ) {
			$hooks[] = array(
				'hook'   => "admin_head-$hook",
				'method' => 'output_tinymce_state'
			);
		}

		return $hooks;
	}
}
