<?php
namespace Intraxia\Gistpen\View;

use Intraxia\Gistpen\Contract\Templating;
use Intraxia\Gistpen\Model\Language;
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
	 * Initialize the class and set its properties.
	 *
	 * @since    0.5.0
	 *
	 * @param string $url
	 */
	public function __construct( Templating $tmpl, $url ) {
		$this->tmpl = $tmpl;
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
		echo $this->tmpl->render( 'page/tinymce/initial', array(
			'globals' => array(
				'languages'  => Language::$supported,
				'root'       => esc_url_raw( rest_url() . 'intraxia/v1/gistpen/' ),
				'nonce'      => wp_create_nonce( 'wp_rest' ),
				'url'        => $this->url,
				'ace_themes' => Edit::$ace_themes,
				'ace_widths' => array( 1, 2, 4, 8 ),
				'statuses'   => get_post_statuses(),
				'themes'     => array(
					'default'                         => __( 'Default', 'wp-gistpen' ),
					'dark'                            => __( 'Dark', 'wp-gistpen' ),
					'funky'                           => __( 'Funky', 'wp-gistpen' ),
					'okaidia'                         => __( 'Okaidia', 'wp-gistpen' ),
					'tomorrow'                        => __( 'Tomorrow', 'wp-gistpen' ),
					'twilight'                        => __( 'Twilight', 'wp-gistpen' ),
					'coy'                             => __( 'Coy', 'wp-gistpen' ),
					'cb'                              => __( 'CB', 'wp-gistpen' ),
					'ghcolors'                        => __( 'GHColors', 'wp-gistpen' ),
					'pojoaque'                        => __( 'Projoaque', 'wp-gistpen' ),
					'xonokai'                         => __( 'Xonokai', 'wp-gistpen' ),
					'base16-ateliersulphurpool-light' => __( 'Ateliersulphurpool-Light', 'wp-gistpen' ),
					'hopscotch'                       => __( 'Hopscotch', 'wp-gistpen' ),
					'atom-dark'                       => __( 'Atom Dark', 'wp-gistpen' ),
				),
			)
		));
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
