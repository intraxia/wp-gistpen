<?php

namespace Intraxia\Gistpen\Http;

use Intraxia\Gistpen\Database\EntityManager;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

class CommitController {

	/**
	 * Database service.
	 *
	 * @var EntityManager
	 */
	private $em;

	/**
	 * CommitController constructor.
	 *
	 * @param EntityManager $em
	 */
	public function __construct( EntityManager $em ) {
		$this->em = $em;
	}

	/**
	 * Retrieves a collection of Commits based on the provided params.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function index( WP_REST_Request $request ) {
		$collection = $this->em->find_by( EntityManager::COMMIT_CLASS, array(
			'repo_id' => $request->get_param( 'repo_id' ),
		) );

		if ( is_wp_error( $collection ) ) {
			$collection->add_data( array( 'status' => 500 ) );

			return $collection;
		}

		return new WP_REST_Response( $collection->serialize(), 200 );
	}
}
