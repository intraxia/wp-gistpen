<?php
namespace Intraxia\Gistpen\Http;

use Intraxia\Gistpen\Options\User;
use InvalidArgumentException;
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
		return new WP_REST_Response( $this->user->all( get_current_user_id() ) );
	}

	/**
	 * Update the user's options.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function update( WP_REST_Request $request ) {
		try {
			return new WP_REST_Response( $this->user->patch( get_current_user_id(), $request->get_params() ), 200 );
		} catch ( InvalidArgumentException $e ) {
			return new WP_Error( 'invalid_params', __( 'Invalid params.', 'wp-gistpen' ), array( 'status' => 400 ) );
		}
	}
}
