<?php
namespace Intraxia\Gistpen\Params;

use Intraxia\Gistpen\Model\Language;
use Intraxia\Gistpen\View\Edit;
use Intraxia\Jaxion\Contract\Core\HasFilters;

class Globals implements HasFilters {
	/**
	 * Website url.
	 *
	 * @var string
	 */
	private $url;

	/**
	 * Globals constructor.
	 *
	 * @param string $url
	 */
	public function __construct( $url ) {
		$this->url = $url;
	}

	/**
	 * Add globals key to params array.
	 *
	 * @param array $params Current params array.
	 *
	 * @return array
	 */
	public function apply_globals( $params ) {
		$params['globals'] = array(
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
		);

		return $params;
	}

	/**
	 * @inheritDoc
	 */
	public function filter_hooks() {
		return array(
			array(
				'hook'   => 'params.state.button',
				'method' => 'apply_globals',
			),
		);
	}
}
