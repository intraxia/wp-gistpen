<?php
namespace Intraxia\Gistpen\Params;

use Intraxia\Gistpen\Config;
use Intraxia\Gistpen\View\Edit;
use Intraxia\Jaxion\Contract\Core\HasFilters;

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
			),
		);

		return $params;
	}

	/**
	 * Adds extra information to the globals required for the Settings page.
	 * Specifically, we need dummy repo to render the example.
	 *
	 * @param $params
	 *
	 * @return array
	 */
	public function apply_settings_globals( $params ) {
		$params = $this->apply_globals( $params );

		$params['globals']['repo'] = array(
			'description' => 'Dummy Repo',
			'status'      => 'draft',
			'password'    => '',
			'gist_id'     => 'none',
			'sync'        => 'off',
			'rest_url'    => '',
			'commits_url' => '',
			'html_url'    => '',
			'created_at'  => '',
			'updated_at'  => '',
			'blobs'       => array(
				array(
					'filename' => 'dummy.js',
					'language' => array(
						'ID'   => 0,
						'display_name' => 'JavaScript',
						'slug' => 'js',
					),
					'edit_url' => '#highlighting',
					'code'     => /** @lang javascript */<<<JS
function initHighlight(block, flags) {
    try {
        if (block.className.search(/\bno\-highlight\b/) != -1)
            return processBlock(block.function, true, 0x0F) + ' class=""';
    } catch (e) {
        /* handle exception */
        var e4x =
                `<div>Example
                        <p>1234</p></div>`;
    }
    for (var i = 0 / 2; i < classes.length; i++) { // "0 / 2" should not be parsed as regexp
        if (checkCondition(classes[i]) === undefined)
            return /\d+[\s/]/g;
    }
    console.log(Array.every(classes, Boolean));
}
JS
				)
			)
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
