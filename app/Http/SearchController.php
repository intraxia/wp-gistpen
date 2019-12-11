<?php
namespace Intraxia\Gistpen\Http;

use Intraxia\Jaxion\Contract\Axolotl\EntityManager;
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
		$args = 'repo' === $request->get_param( 'type' )
			? [
				\Intraxia\Gistpen\Model\Repo::class,
				[
					'with' => [
						'blobs' => [
							'with' => 'language',
						],
					],
				],
			]
			: [
				\Intraxia\Gistpen\Model\Blob::class,
				[ 'with' => 'language' ],
			];

		$search_term = $request->get_param( 's' );

		if ( $search_term ) {
			$args[1]['s'] = $search_term;
		}

		$result = $this->em->find_by( $args[0], $args[1] );

		if ( is_wp_error( $result ) ) {
			$result->add_data( array( 'status' => 500 ) );

			return $result;
		}

		return new WP_REST_Response( $result->serialize() );
	}
}
