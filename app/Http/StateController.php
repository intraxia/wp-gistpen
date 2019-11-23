<?php

namespace Intraxia\Gistpen\Http;

use Intraxia\Gistpen\Database\EntityManager;
use Intraxia\Gistpen\Model\Commit;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Controller for access Repo state.
 */
class StateController {

	/**
	 * Database service.
	 *
	 * @var EntityManager
	 */
	private $em;

	/**
	 * StateController constructor.
	 *
	 * @param EntityManager $em
	 */
	public function __construct( EntityManager $em ) {
		$this->em = $em;
	}

	/**
	 * Retrieves a collection of States based on the provided params.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function index( WP_REST_Request $request ) {
		$commit = $this->em->find( EntityManager::COMMIT_CLASS, $request->get_param( 'commit_id' ), array(
			'with' => 'states',
		) );

		if ( is_wp_error( $commit ) ) {
			$commit->add_data( array( 'status' => 500 ) );

			return $commit;
		}

		return new WP_REST_Response( $commit->states->serialize(), 200 );
	}
}
