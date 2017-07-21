<?php
namespace Intraxia\Gistpen\Http;

use Intraxia\Gistpen\Database\EntityManager;
use Intraxia\Gistpen\Model\Blob;
use Intraxia\Gistpen\Model\Repo;
use Intraxia\Jaxion\Contract\Core\HasFilters;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Class BlobController
 *
 * @package    Intraxia\Gistpen
 * @subpackage Http
 */
class BlobController implements HasFilters {
	/**
	 * Database interface service.
	 *
	 * @var EntityManager
	 */
	protected $em;

	/**
	 * RepoController constructor.
	 *
	 * @param EntityManager $database
	 */
	public function __construct( EntityManager $em ) {
		$this->em = $em;
	}

	/**
	 * Fetches the requested Blob and sets up the appropriate response.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function raw( WP_REST_Request $request ) {
		/** @var Repo|WP_Error $repo */
		$blob = $this->em->find( EntityManager::BLOB_CLASS, $request->get_param( 'blob_id' ), array(
			'repo_id' => $request->get_param( 'repo_id' ),
		) );

		if ( is_wp_error( $repo ) ) {
			$repo->add_data( array( 'status' => 404 ) );

			return $repo;
		}

		$response = new WP_REST_Response( $blob->code, 200 );
		$response->header( 'Content-Type', 'text/plain; charset=utf-8' );
		$response->header( 'Content-Security-Policy', "default-src 'none'; style-src 'unsafe-inline'" );

		return $response;
	}

	/**
	 * Overwrites the default API server response when serving raw,
	 * ensuring the response isn't JSON encoded and echoed directly.
	 *
	 * @param boolean          $served
	 * @param WP_REST_Response $response
	 * @param WP_REST_Request  $request
	 *
	 * @return bool
	 */
	public function serve_raw( $served, WP_REST_Response $response, WP_REST_Request $request ) {
		if ( $served ||
			$request->get_method() !== 'GET' ||
			! preg_match('/\/intraxia\/v1\/gistpen\/repos\/\d+\/blobs\/\d+\/raw/', $request->get_route() )
		) {
			return $served;
		}

		echo $response->get_data();

		return true;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return array[]
	 */
	public function filter_hooks() {
		return array(
			array(
				'hook'   => 'rest_pre_serve_request',
				'method' => 'serve_raw',
				'args'   => 3,
			)
		);
	}
}
