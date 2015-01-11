<?php
namespace WP_Gistpen\Controller;

use WP_Gistpen\Account\Gist;
use WP_Gistpen\Model\Zip;
use WP_Gistpen\Facade\Database;
use WP_Gistpen\Facade\Adapter;
use \WP_CLI;

/**
 * Manages the data to keep the database in sync with Gist.
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Sync {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.5.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.5.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Database Facade object
	 *
	 * @var Database
	 * @since 0.5.0
	 */
	private $database;

	/**
	 * Adapter Facade object
	 *
	 * @var Adapter
	 * @since  0.5.0
	 */
	private $adapter;

	/**
	 * Gist Account object
	 *
	 * @var    Gist
	 * @since  0.5.0
	 */
	public $gist;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.5.0
	 * @var      string    $plugin_name       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->database = new Database( $this->plugin_name, $this->version );
		$this->adapter = new Adapter( $this->plugin_name, $this->version );

		$this->gist = new Gist( $this->plugin_name, $this->version );

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
		if ( false === cmb2_get_option( $this->plugin_name, '_wpgp_gist_token' ) ) {
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
	 * @param Commit              $commit    Commit object
	 * @return string|\WP_Error               Gist ID on success, WP_Error on failure
	 * @since 0.5.0
	 */
	protected function create_gist( $commit ) {
		$response = $this->gist->create_gist( $commit );

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
	 * @return string|WP_Error Gist ID on success, WP_Error on failure
	 * @since 0.5.0
	 */
	protected function update_gist( $commit ) {
		$response = $this->gist->update_gist( $commit );

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

		$response = $this->gist->get_gist( $gist_id );

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
