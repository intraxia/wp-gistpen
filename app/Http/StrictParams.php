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

		$invalid_params = [];

		foreach ( $request->get_params() as $key => $value ) {
			if ( ! isset( $attributes['args'][ $key ] ) ) {
				$invalid_params[ $key ] = sprintf(
					__( 'Param "%s" is not a valid request param.', 'wp-gistpen' ),
					$key
				);
			}
		}

		if ( $invalid_params ) {
			if ( is_wp_error( $response ) ) {
				$data = $response->get_error_data();
				$invalid_params = array_merge( $data['params'], $invalid_params );
			}

			return new WP_Error(
				'rest_invalid_param',
				sprintf(
					__( 'Invalid parameter(s): %s' ),
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
