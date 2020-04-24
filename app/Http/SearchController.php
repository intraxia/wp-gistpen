<?php
namespace Intraxia\Gistpen\Http;

use Intraxia\Jaxion\Contract\Axolotl\EntityManager;
use Intraxia\Gistpen\Model\Blob;
use Intraxia\Gistpen\Model\Repo;
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
	 * Handles a GET request on the Blob Search endpoint.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function blobs( WP_REST_Request $request ) {
		$args        = [ 'with' => 'language' ];
		$search_term = $request->get_param( 's' );

		if ( $search_term ) {
			$args['s'] = $search_term;
		}

		$result = $this->em->find_by( Blob::class, $args );

		if ( is_wp_error( $result ) ) {
			$result->add_data( array( 'status' => 500 ) );

			return $result;
		}

		return new WP_REST_Response( $result->map(function( Blob $repo ) {
			return [
				'ID'            => $repo->ID,
				'filename'      => $repo->filename,
				'code'          => $repo->code,
				'repo_id'       => $repo->repo_id,
				'language'      => $repo->language->serialize(),
				'rest_url'      => $repo->rest_url,
				'repo_rest_url' => $repo->repo_rest_url,
			];
		})->to_array() );
	}

	/**
	 * Handles a GET request on the repo Search endpoint.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function repos( WP_REST_Request $request ) {
		$args = [ 'with' => [ 'blobs' => [] ] ];

		$search_term = $request->get_param( 's' );

		if ( $search_term ) {
			$args['s'] = $search_term;
		}

		$result = $this->em->find_by( Repo::class, $args );

		if ( is_wp_error( $result ) ) {
			$result->add_data( array( 'status' => 500 ) );

			return $result;
		}

		return new WP_REST_Response( $result->map(function( Repo $repo ) {
			return [
				'ID'          => $repo->ID,
				'description' => $repo->description,
				'slug'        => $repo->slug,
				'status'      => $repo->status,
				'password'    => $repo->password,
				'gist_id'     => $repo->gist_id,
				'gist_url'    => $repo->gist_url,
				'sync'        => $repo->sync,
				'blobs'       => $repo->blobs->map(function( Blob $blob ) {
						return [
							'ID'       => $blob->ID,
							'filename' => $blob->filename,
							'rest_url' => $blob->rest_url,
						];
				} )->to_array(),
				'rest_url'    => $repo->rest_url,
				'commits_url' => $repo->commits_url,
				'html_url'    => $repo->html_url,
				'created_at'  => $repo->created_at,
				'updated_at'  => $repo->updated_at,
			];
		})->to_array() );
	}
}
