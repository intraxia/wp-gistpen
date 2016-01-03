<?php
namespace Intraxia\Gistpen\Http;

use Exception;
use Intraxia\Gistpen\Options\User;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Class UserController
 *
 * @package    Intraxia\Gistpen
 * @subpackage Http
 */
class UserController {
	/**
	 * User options.
	 *
	 * @var User
	 */
	protected $user;

	/**
	 * {@inheritDoc}
	 *
	 * @param User $user
	 */
	public function __construct( User $user ) {
		$this->user = $user;
	}

	/**
	 * View all of the user's currently enabled options.
	 *
	 * @return WP_REST_Response
	 */
	public function view() {
		return new WP_REST_Response( $this->user->all() );
	}

	/**
	 * Update the user's options.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function update( WP_REST_Request $request ) {
		$params = $request->get_params();
		$errors = array();

		foreach ( $params as $key => $value ) {
			try {
				$this->user->set( $key, $value );
			} catch ( Exception $e ) {
				$errors[] = new WP_Error( 'invalid_key', __( 'Invalid key', 'wp-gistpen' ), $key );
			}
		}

		$data = $this->user->all();
		$data['errors'] = $errors;

		return new WP_REST_Response( $data );
	}
}
