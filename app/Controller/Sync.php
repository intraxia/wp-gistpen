<?php
namespace WP_Gistpen\Controller;

use WP_Gistpen\Account\Gist;
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
	 * Exports all the unsynced Gistpens to Gist
	 *
	 * @return string|WP_Error Gist ID on success, WP_Error on failure
	 * @since 0.5.0
	 */
	public function export_gistpen( $zip_id ) {
		$zip = $this->database->query( 'head' )->by_id( $zip_id );

		if ( is_wp_error( $zip ) ){
			return $zip;
		}

		if ( 'none' !== $zip->get_gist_id() ) {
			return $zip;
		}

		$response = $this->gist->create_gist( $zip );

		if ( is_wp_error( $response ) ){
			return $response;
		}

		$result = $this->database->persist( 'head' )->set_gist_id( $zip_id, $result );

		if ( is_wp_error( $result ) ){
			return $result;
		}

		return $zip;
	}
}
