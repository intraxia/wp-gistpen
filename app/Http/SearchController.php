<?php
namespace Intraxia\Gistpen\Http;

use Intraxia\Gistpen\Facade\Adapter;
use Intraxia\Gistpen\Facade\Database;
use WP_REST_Request;

/**
 * Class Search
 *
 * @package Intraxia\Gistpen
 * @subpackage Controller
 */
class SearchController {
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
	 * Handles a GET request on the Search endpoint.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function get( WP_REST_Request $request ) {
		$models = ( $term = $request->get_param( 's' ) ) ? $this->database->query()->by_string( $term ) : $this->database->query()->by_recent();

		if ( is_wp_error( $models ) ) {
			return $models;
		}

		return $this->adapter->build( 'api' )->by_array_of_models( $models );
	}
}
