<?php
namespace Intraxia\Gistpen\Templating;

use Exception;
use Intraxia\Gistpen\Config;
use LightnCandy\LightnCandy;
use Intraxia\Gistpen\Contract\Templating;
use LightnCandy\SafeString;

/**
 * Class Handlebars.
 *
 * Templating Service for Handlebars.
 *
 * @package    Intraxia\Gistpen
 * @subpackage Templating
 */
class Handlebars implements Templating {
	/**
	 * Client path.
	 *
	 * @var string
	 */
	protected $client;

	/**
	 * App Config service.
	 *
	 * @var Config
	 */
	private $config;

	/**
	 * Handlebars constructor.
	 *
	 * @param Config $config
	 * @param string $client
	 */
	public function __construct( Config $config, $client ) {
		$this->config = $config;
		$this->client = $client;
	}

	/**
	 * Generates a string from the handlebars partials and provided data.
	 *
	 * @param string $partial
	 * @param array  $data
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function render( $partial, array $data ) {
		$phpStr = LightnCandy::compile( file_get_contents( $this->client . $partial . '.hbs' ), array(
			'flags'           => LightnCandy::FLAG_HANDLEBARSJS_FULL | LightnCandy::FLAG_RUNTIMEPARTIAL,
			'partialresolver' => function ( $cx, $name ) {
				$filename = $this->client . $name . '.hbs';

				if ( file_exists( $filename ) ) {
					return file_get_contents( $filename );
				}

				return "[partial (file:$filename) not found]";
			},
			'helpers'         => array(
				'compare'    => function ( $first, $second, $options ) {
					if ( $first === $second ) {
						return $options['fn']( $options['data'] );
					} else {
						return $options['inverse']( $options['data'] );
					}
				},
				'json'       => function( $context ) {
					return new SafeString( wp_json_encode( $context ) );
				},
				'prism_slug' => function ( $slug ) {
					$languages = $this->config->get_config_json( 'languages' );
					$map = $languages['aliases'];

					if ( array_key_exists( $slug, $map ) ) {
						$slug = $map[ $slug ];
					}

					return $slug;
				},
				'link' => function (/* $search_key, $target */) {
					return '#';
				},
				'join' => function () {
					$args = func_get_args();
					array_pop( $args );
					$glue = array_pop( $args );

					return implode( $glue, $args );
				}
			)
		) );

		// @todo swap out deprecated prepare for custom solution (eval? write to filesystem?).
		$render = LightnCandy::prepare( $phpStr );

		if ( ! ( $render instanceof \Closure ) ) {
			throw new Exception('Invalid PHP generated. Check Handlebars template for invalid syntax.');
		}

		return $render->call( $this, $data );
	}
}
