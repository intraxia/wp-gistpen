<?php
namespace Intraxia\Gistpen\Http;

use Intraxia\Gistpen\Facade\Adapter;
use Intraxia\Gistpen\Facade\Database;
use Intraxia\Gistpen\Model\File;
use Intraxia\Gistpen\Model\Zip;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Class Zip
 *
 * @package    Intraxia\Gistpen
 * @subpackage Controller
 */
class ZipController {
	/**
	 * Database Facade object
	 *
	 * @var Database
	 * @since 0.6.0
	 */
	public $database;

	/**
	 * Adapter Facade object
	 *
	 * @var  Adapter
	 * @since 0.6.0
	 */
	protected $adapter;

	/**
	 * Instantiates a new Search controller.
	 *
	 * @param Database $database
	 * @param Adapter  $adapter
	 */
	public function __construct( Database $database, Adapter $adapter ) {
		$this->database = $database;
		$this->adapter  = $adapter;
	}

	/**
	 * Returns the Zip resource requested by id.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function view( WP_REST_Request $request ) {
		$id = $request->get_param( 'id' );

		/** @var \Intraxia\Gistpen\Database\Query\Head $query Head query. */
		$query = $this->database->query( 'head' );

		$zip = $query->by_id( $id );

		if ( is_wp_error( $zip ) ) {
			return $zip;
		}

		/** @var \Intraxia\Gistpen\Adapter\Api $adapter Heade query. */
		$adapter = $this->adapter->build( 'api' );

		return $adapter->by_model( $zip );
	}

	/**
	 * Creates and returns a new Zip resource.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return \WP_Error|array
	 *
	 * @throws \Exception
	 */
	public function create( WP_REST_Request $request ) {
		/** @var Zip $zip */
		$zip = $this->adapter->build( 'zip' )->by_array( array(
			'description' => $request->get_param( 'description' ),
			'status'      => $request->get_param( 'status' ),
		) );

		foreach ( $request->get_param( 'files' ) as $file_data ) {
			/** @var File $file */
			$file = $this->adapter->build( 'file' )->by_array( $file_data );
			$file->set_language( $this->adapter->build( 'language' )->by_slug( $file_data['language'] ) );

			$zip->add_file( $file );
			unset( $file );
		}

		$results = $this->database->persist( 'head' )->by_zip( $zip );

		if ( is_wp_error( $results ) ) {
			return $results;
		}

		$result = $this->database->persist( 'commit' )->by_ids( $results );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$zip->set_ID( $results['zip'] );

		/**
		 * After update action hook.
		 *
		 * Can hook in and modify the WP_REST_Response object
		 * if something happens that results in an error.
		 * This response is important when communicating
		 * with GitHub's API. If something goes wrong,
		 * we want to return that error to the user
		 * so they can go fix it.
		 */
		do_action( 'wpgp_zip_created', $response = new WP_REST_Response( array( 'zip' => $zip ) ) );

		return $response;
	}
}
