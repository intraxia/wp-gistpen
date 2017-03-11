<?php
namespace Intraxia\Gistpen\Templating;

use Exception;
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
	 * Handlebars constructor.
	 *
	 * @param string $client
	 */
	public function __construct( $client ) {
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
					$map = array(
						'js'        => 'javascript',
						'sass'      => 'scss',
						'py'        => 'python',
						'html'      => 'markup',
						'xml'       => 'markup',
						'plaintext' => 'none',
					);

					if ( array_key_exists( $slug, $map ) ) {
						$slug = $map[ $slug ];
					}

					return $slug;
				}
			)
		) );

		// @todo swap out deprecated prepare for custom solution (eval? write to filesystem?).
		$render = LightnCandy::prepare( $phpStr );

		if ( ! ( $render instanceof \Closure ) ) {
			throw new Exception('Invalid PHP generated. Check Handlebars template for invalid syntax.');
		}

		return $render( $data );
	}
}
