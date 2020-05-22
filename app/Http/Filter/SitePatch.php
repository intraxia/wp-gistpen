<?php
namespace Intraxia\Gistpen\Http\Filter;

use Intraxia\Jaxion\Contract\Http\Filter as FilterContract;
use Intraxia\Gistpen\Options\Site;
use Intraxia\Gistpen\Params\Globals;
use WP_Error;

/**
 * Class Filter\SitePatch
 *
 * @package Intraxia\Gistpen\Http
 * @subpackage Filter
 */
class SitePatch implements FilterContract {
	/**
	 * Generates argument rules.
	 *
	 * Returns an array matching the WP-API format for argument rules,
	 * including sanitization, validation, required, or defaults.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'prism' => [
				'description'       => __( 'Prism syntax highlighting configuration', 'wp-gistpen' ),
				'required'          => false,
				'type'              => 'object',
				'properties' => [

				],
				'additionalProperties' => false,
			],
			'gist'  => [
				'description'       => __( 'Gist sync configuration', 'wp-gistpen' ),
				'required'          => false,
				'type'              => 'array',
				'sanitize_callback' => [ $this, 'sanitize_gist' ],
			],
		];
	}

	/**
	 * Sanitize the prism key.
	 *
	 * @param  array $prism
	 * @return array
	 */
	public function sanitize_prism( array $prism ) {
		foreach ( $prism as $key => $value ) {
			if ( ! array_key_exists( $key, Site::$defaults['prism'] ) ) {
				return new WP_Error(
					'invalid_rest_param',
					sprintf(
						/* translators: prism option key */
						__( 'Param "prism.%s" is not a valid request param.', 'wp-gistpen' ),
						$key
					)
				);
			}

			if ( 'theme' === $key ) {
				if ( ! is_string( $value ) ) {
					return new WP_Error(
						'invalid_rest_param',
						__( 'Param "prism.theme" is not a string.', 'wp-gistpen' )
					);
				}

				if ( ! in_array( $value, Globals::$themes, true ) ) {
					return new WP_Error(
						'invalid_rest_param',
						__( 'Param "prism.theme" is not a valid theme.', 'wp-gistpen' )
					);
				}
			}

			if ( in_array( $key, [ 'line-numbers', 'show-invisibles' ], true ) ) {
				if ( ! is_bool( $value ) ) {
					return new WP_Error(
						'invalid_rest_param',
						sprintf(
							/* translators: prism option key */
							__( 'Param "prism.%s" is not a boolean.', 'wp-gistpen' ),
							$key
						)
					);
				}
			}
		}

		return $prism;
	}

	/**
	 * Sanitize the gist key.
	 *
	 * @param  array $gist
	 * @return array
	 */
	public function sanitize_gist( array $gist ) {
		foreach ( $gist as $key => $value ) {
			if ( ! array_key_exists( $key, Site::$defaults['gist'] ) ) {
				return new WP_Error(
					'invalid_rest_param',
					sprintf(
						/* translators: gist option key */
						__( 'Param "gist.%s" is not a valid request param.', 'wp-gistpen' ),
						$key
					)
				);
			}

			if ( 'token' === $key && ! is_string( $value ) ) {
							return new WP_Error(
								'invalid_rest_param',
								__( 'Param "gist.token" is not a string.', 'wp-gistpen' )
							);
			}
		}

		return $gist;
	}
}
