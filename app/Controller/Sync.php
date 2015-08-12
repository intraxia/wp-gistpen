<?php
namespace Intraxia\Gistpen\Controller;

use Intraxia\Gistpen\Client\Gist;
use Intraxia\Gistpen\Model\Zip;
use Intraxia\Gistpen\Facade\Database;
use Intraxia\Gistpen\Facade\Adapter;
use \WP_CLI;

/**
 * Manages the data to keep the database in sync with Gist.
 *
 * @package    Intraxia\Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Sync {

	/**
	 * Filter hooks for Sync controller.
	 *
	 * @var array
	 */
	public $filters = array(
		array(
			'hook' => 'wpgp_after_update',
			'method' => 'export_gistpen',
		)
	);

	/**
	 * Database Facade object
	 *
	 * @var Database
	 * @since 0.5.0
	 */
	protected $database;

	/**
	 * Adapter Facade object
	 *
	 * @var Adapter
	 * @since  0.5.0
	 */
	protected $adapter;

	/**
	 * Gist Client object
	 *
	 * @var    Gist
	 * @since  0.5.0
	 */
	public $gist;

    /**
     * Initialize the class and set its properties.
     *
     * @since    0.5.0
     *
     * @param Gist $gist
     */
    public function __construct(Gist $gist)
    {
        $this->database = new Database();
        $this->adapter = new Adapter();

        $this->gist = $gist;
    }

	/**
	 * Exports a Gistpen to Gist based on its ID
	 *
	 * If the Zip doesn't have a Gist ID, create a new Gist,
	 * or update an existing Gist if it does.
	 *
	 * @param  int              $zip_id   Zip ID of exporting Gistpen
	 * @return string|\WP_Error           Zip ID on success, WP_Error on failure
	 */
	public function export_gistpen( $zip_id ) {
		if ( false === cmb2_get_option( \Gistpen::$plugin_name, '_wpgp_gist_token' ) ) {
			return $zip_id;
		}

		$commit = $this->database->query( 'commit' )->latest_by_head_id( $zip_id );

		if ( 'on' !== $commit->get_sync() || 'none' !== $commit->get_gist_id() ) {
			return $zip_id;
		}

		if ( 'none' === $commit->get_head_gist_id() ) {
			$result = $this->create_gist( $commit );
		} else {
			$result = $this->update_gist( $commit );
		}

		if ( is_wp_error( $result ) ){
			return $result;
		}

		return $zip_id;
	}

	/**
	 * Creates a new Gistpen on Gist
	 *
	 * @param \Gistpen\Model\Commit\Meta $commit
	 * @return string|\WP_Error
	 * @since 0.5.0
	 */
	protected function create_gist( $commit ) {
		$response = $this->gist->create( $commit );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$result = $this->database->persist( 'head' )->set_gist_id( $commit->get_head_id(), $response['id'] );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$result = $this->database->persist( 'commit' )->set_gist_id( $commit->get_ID(), $response['history'][0]['version'] );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return $response;
	}

	/**
	 * Updates an existing Gistpen on Gist
	 *
	 * @param \Gistpen\Model\Commit\Meta $commit
	 * @return string|\WP_Error Gist ID on success, WP_Error on failure
	 * @since 0.5.0
	 */
	protected function update_gist( $commit ) {
		$response = $this->gist->update( $commit );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$result = $this->database->persist( 'commit' )->set_gist_id( $commit->get_ID(), $response['history'][0]['version'] );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return $response;
	}

	/**
	 * Imports a Gist into Gistpen by ID
	 *
	 * @param  string             $gist_id    Gist ID
	 * @return string|\WP_Error               Gist ID on success, WP_Error on failure
	 * @since  0.5.0
	 */
	public function import_gist( $gist_id ) {
		// Exit if this gist has already been imported
		$query = $this->database->query( 'head' )->by_gist_id( $gist_id );

		if ( $query instanceof Zip ) {
			return $query;
		}

		$response = $this->gist->get( $gist_id );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$zip = $response['zip'];
		$version = $response['version'];
		unset( $response );

		$result = $this->database->persist( 'head' )->by_zip( $zip );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$ids = $result;
		unset( $result );

		$result = $this->database->persist( 'commit' )->by_ids( $ids );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$result = $this->database->persist( 'commit' )->set_gist_id( $result, $version );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return $ids['zip'];
	}
}
