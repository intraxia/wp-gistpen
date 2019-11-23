<?php
namespace Intraxia\Gistpen\Client;

use Intraxia\Gistpen\Options\Site;
use Requests_Response;
use Requests_Session;
use WP_Error;

/**
 * Gist client service.
 *
 * @package    Intraxia\Gistpen
 * @subpackage Client
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 */
class Gist {
	/**
	 * Gist API endpoint url.
	 */
	const API = 'https://api.github.com/gists';

	/**
	 * Site Options service.
	 *
	 * @var Site
	 */
	private $site;

	/**
	 * HTTP client.
	 *
	 * @var Requests_Session
	 */
	private $http;

	/**
	 * Gist constructor.
	 *
	 * @param Site             $site
	 * @param Requests_Session $http
	 */
	public function __construct( Site $site, Requests_Session $http ) {
		$this->site = $site;
		$this->http = $http;
	}

	/**
	 * Get a list of all the user's remote gists.
	 *
	 * @return Requests_Response|WP_Error
	 */
	public function all() {
		$token = $this->get_token();

		if ( is_wp_error( $token ) ) {
			return $token;
		}

		$response = $this->http->get( self::API, $this->get_default_headers( $token ) );

		return $this->process_response( $response );
	}

	/**
	 * Get a single remote gist.
	 *
	 * @param int $id
	 *
	 * @return WP_Error|Requests_Response
	 */
	public function one( $id ) {
		$token = $this->get_token();

		if ( is_wp_error( $token ) ) {
			return $token;
		}

		$response = $this->http->get(
			self::API . '/' . $id,
			$this->get_default_headers( $token )
		);

		return $this->process_response( $response );
	}

	/**
	 * Cretate a new remote gist.
	 *
	 * @param array $data
	 *
	 * @return Requests_Response|WP_Error
	 */
	public function create( array $data ) {
		$token = $this->get_token();

		if ( is_wp_error( $token ) ) {
			return $token;
		}

		$response = $this->http->post(
			self::API,
			$this->get_post_headers( $token ),
			wp_json_encode( $data )
		);

		return $this->process_response( $response );
	}

	/**
	 * Update a remote gist.
	 *
	 * @param string $id
	 * @param array  $data
	 *
	 * @return Requests_Response|WP_Error
	 */
	public function update( $id, array $data ) {
		$token = $this->get_token();

		if ( is_wp_error( $token ) ) {
			return $token;
		}

		$response = $this->http->patch(
			self::API . '/' . $id,
			$this->get_post_headers( $token ),
			wp_json_encode( $data )
		);

		return $this->process_response( $response );
	}

	/**
	 * Delete a remote gist.
	 *
	 * @param string $id
	 *
	 * @return Requests_Response|WP_Error
	 */
	public function delete( $id ) {
		$token = $this->get_token();

		if ( is_wp_error( $token ) ) {
			return $token;
		}

		$response = $this->http->delete(
			self::API . '/' . $id,
			$this->get_default_headers( $token )
		);

		return $this->process_response( $response );
	}

	/**
	 * Get the site token or WP_Error if not set.
	 *
	 * @return WP_Error|string
	 */
	private function get_token() {
		$gist = $this->site->get( 'gist' );

		if ( ! $gist['token'] ) {
			return new WP_Error( 'auth_error', __( 'No token saved.', 'wp-gistpen' ) );
		}

		return $gist['token'];
	}

	/**
	 * Get combined headers used for POST requests.
	 *
	 * @param string $token
	 *
	 * @return array
	 */
	private function get_post_headers( $token ) {
		return array_merge( $this->get_default_headers( $token ), array(
			'Content-Type' => 'application/json; charset=utf-8',
		) );
	}

	/**
	 * Get the default headers used for Gist requests.
	 *
	 * @param string $token
	 *
	 * @return array
	 */
	private function get_default_headers( $token ) {
		return array(
			'Authorization' => 'token ' . $token,
			'Accept'        => 'application/json',
		);
	}

	/**
	 * Processes the response into a WP_Error if failed.
	 *
	 * @param Requests_Response $response
	 *
	 * @return Requests_Response|WP_Error
	 */
	private function process_response( Requests_Response $response ) {
		$json = $response->json = json_decode( $response->body );

		if ( ! $response->success ) {
			// 4XX errors: client-side problems
			if ( $response->status_code >= 400 && $response->status_code < 500 ) {
				if ( $response->status_code === 401 ) {
					return new WP_Error(
						'auth_error',
						sprintf(
							__( 'Authorization error. Message: %s', 'wp-gistpen' ),
							$json->message
						)
					);
				}

				return new WP_Error(
					'client_error',
					sprintf(
						__( 'Error sending request. Message: %s', 'wp-gistpen' ),
						$json->message
					)
				);
			}

			// 5XX error: server-side problems
			if ( $response->status_code >= 500 ) {
				return new WP_Error( 'server_error', __( 'Server error.', 'wp-gistpen' ) );
			}
		}

		return $response;
	}
}
