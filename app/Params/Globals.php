<?php
namespace Intraxia\Gistpen\Params;

use Intraxia\Jaxion\Core\Config;
use Intraxia\Gistpen\View\Edit;
use Intraxia\Jaxion\Contract\Core\HasFilters;

/**
 * Globals service to manage its slice of state.
 */
class Globals implements HasFilters {

	/**
	 * App Config service.
	 *
	 * @var Config
	 */
	private $config;

	/**
	 * Globals constructor.
	 *
	 * @param Config $config
	 */
	public function __construct( Config $config ) {
		$this->config = $config;
	}

	/**
	 * Add globals key to params array.
	 *
	 * @param array $params Current params array.
	 *
	 * @return array
	 */
	public function apply_globals( $params ) {
		$languages = $this->config->get_config_json( 'languages' );

		$params['globals'] = array(
			'languages'  => $languages['list'],
			'root'       => esc_url_raw( rest_url() . 'intraxia/v1/gistpen/' ),
			'nonce'      => wp_create_nonce( 'wp_rest' ),
			'url'        => $this->config->url,
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
				'duotone-dark'                    => __( 'Duotone Dark', 'wp-gistpen' ),
				'duotone-sea'                     => __( 'Duotone Sea', 'wp-gistpen' ),
				'duotone-space'                   => __( 'Duotone Space', 'wp-gistpen' ),
				'duotone-earth'                   => __( 'Duotone Earth', 'wp-gistpen' ),
				'duotone-forest'                  => __( 'Duotone Forest', 'wp-gistpen' ),
				'duotone-light'                   => __( 'Duotone Light', 'wp-gistpen' ),
				'vs'                              => __( 'VS', 'wp-gistpen' ),
				'darcula'                         => __( 'Darcula', 'wp-gistpen' ),
				'a11y-dark'                       => __( 'a11y Dark', 'wp-gistpen' ),
			),
		);

		return $params;
	}

	/**
	 * Adds extra information to the globals required for the Settings page.
	 * Specifically, we need dummy repo to render the example.
	 *
	 * @param array $params Current params array.
	 *
	 * @return array
	 */
	public function apply_settings_globals( $params ) {
		$params = $this->apply_globals( $params );

		$params['globals']['demo'] = array(
			'filename' => 'dummy.js',
			'language' => 'javascript',
			'code'     => file_get_contents( __DIR__ . '/demo-code' ),
		);

		return $params;
	}

	/**
	 * {@inheritDoc}
	 */
	public function filter_hooks() {
		return array(
			array(
				'hook'   => 'params.state.button',
				'method' => 'apply_globals',
			),
			array(
				'hook'   => 'params.state.content',
				'method' => 'apply_globals',
			),
			array(
				'hook'   => 'params.state.settings',
				'method' => 'apply_settings_globals',
			),
			array(
				'hook'   => 'params.props.settings',
				'method' => 'apply_settings_globals',
			),
			array(
				'hook'   => 'params.state.edit',
				'method' => 'apply_globals',
			),
		);
	}
}
