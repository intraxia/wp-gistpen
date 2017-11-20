<?php
namespace Intraxia\Gistpen\Http;

use Intraxia\Gistpen\Database\EntityManager;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Class Search
 *
 * @package Intraxia\Gistpen
 * @subpackage Controller
 */
class SearchController {
	/**
	 * Database object
	 *
	 * @var EntityManager
	 * @since 0.6.0
	 */
	public $em;

	/**
	 * Instantiates a new Search controller.
	 *
	 * @param EntityManager $em
	 */
	public function __construct( EntityManager $em ) {
		$this->em = $em;
	}

	/**
	 * Handles a GET request on the Search endpoint.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function get( WP_REST_Request $request ) {
		$args = array(
			'posts_per_page' => 5,
			'with' => 'language',
		);
		$search_term = $request->get_param( 's' );

		if ( $search_term ) {
			$args['s'] = $search_term;
		}

		$blobs = $this->em->find_by( EntityManager::BLOB_CLASS, $args );

		if ( is_wp_error( $blobs ) ) {
			$blobs->add_data( array( 'status' => 500 ) );

			return $blobs;
		}

		return new WP_REST_Response( $blobs->serialize() );
	}
}
