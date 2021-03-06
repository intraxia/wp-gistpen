<?php

namespace Intraxia\Gistpen\Http;

use Intraxia\Jaxion\Contract\Core\HasFilters;
use Intraxia\Gistpen\Model\Commit;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Class StrictParams
 *
 * Removes any unregistered params from incoming requests.
 *
 * @package    Intraxia\Gistpen
 * @subpackage Http
 * @since      2.0.0
 */
class StrictParams implements HasFilters {
	/**
	 * Whitelist of params to allow to pass the strict check.
	 *
	 * @var string[]
	 */
	private static $param_whitelist = [
		'rest_route',
	];

	/**
	 * Removes any arguments from the request that aren't set in its `args.`
	 * This ensures any registered controller has a correctly matching
	 * sanitization param for all the expected parameters.
	 *
	 * @param  null|WP_Error|WP_REST_Response $response The response to send the the BE.
	 *                                                  Probably null, as the request hasn't been handled.
	 * @param  callable                       $handler  Request handler.
	 * @param  WP_REST_Request                $request  The currentl processing request.
	 * @return null|WP_Error|WP_REST_Response           The final response.
	 */
	public function filter_unregistered_params( $response, $handler, WP_REST_Request $request ) {
		if ( strpos( $request->get_route(), 'intraxia/v1/gistpen' ) === false ) {
			return $response;
		}

		$attributes = $request->get_attributes();

		// @TODO(mAAdhaTTah) once args is on all routes,
		// return error if this isn't set,
		// or make this configurable
		if ( ! isset( $attributes['args'] ) ) {
			return $response;
		}

		// We don't want to validate URL params.
		$params         = array_merge( $request->get_body_params(), $request->get_query_params() );
		$invalid_params = [];

		foreach ( $params as $key => $value ) {
			if ( ! isset( $attributes['args'][ $key ] ) && ! in_array( $key, self::$param_whitelist, true ) ) {
				$invalid_params[ $key ] = sprintf(
					/* translators: %s: Request param. */
					__( '%s is not a valid request param.', 'wp-gistpen' ),
					$key
				);
			}
		}

		if ( $invalid_params ) {
			if ( is_wp_error( $response ) ) {
				$data           = $response->get_error_data();
				$invalid_params = array_merge( $data['params'], $invalid_params );
			}

			return new WP_Error(
				'rest_invalid_param',
				sprintf(
					/* translators: %s: Request params, comma-separated. */
					__( 'Invalid parameter(s): %s', 'wp-gistpen' ),
					implode( ', ', array_keys( $invalid_params ) )
				),
				array(
					'status' => 400,
					'params' => $invalid_params,
				)
			);
		}

		return $response;
	}

	/**
	 * {@inheritDoc}
	 */
	public function filter_hooks() {
		return [
			[
				'hook'   => 'rest_request_before_callbacks',
				'method' => 'filter_unregistered_params',
				'args'   => 3,
			],
		];
	}
}
