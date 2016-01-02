<?php
namespace WP_Gistpen\Api;

use WP_Gistpen\Account\Gist;
use WP_Gistpen\Controller\Save;
use WP_Gistpen\Controller\Sync;
use WP_Gistpen\Facade\Database;
use WP_Gistpen\Facade\Adapter;

/**
 * This class handles all of the AJAX responses
 *
 * @package    Ajax
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.4.0
 */
class Ajax {

	/**
	 * Slug for the nonce field
	 *
	 * @var string
	 * @since  0.4.0
	 */
	protected $nonce_field;

	/**
	 * Database Facade object
	 *
	 * @var Database
	 * @since 0.5.0
	 */
	public $database;

	/**
	 * Adapter Facade object
	 *
	 * @var Adapter
	 * @since  0.5.0
	 */
	public $adapter;

	/**
	 * Sync object
	 *
	 * @var Sync
	 * @since  0.5.0
	 */
	public $sync;

	/**
	 * Save object
	 *
	 * @var Save
	 * @since  0.5.0
	 */
	public $save;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.5.0
	 */
	public function __construct() {
		$this->nonce_field = '_ajax_wp_gistpen';

		$this->database = new Database();
		$this->adapter = new Adapter();

		$this->save = new Save();
		$this->sync = new Sync();
		$this->gist = new Gist();
	}

	/**
	 * Retrieves the ACE editor theme from the user meta
	 *
	 * @since 0.5.0
	 */
	public function get_ace_theme() {
		wp_send_json_success( array( 'theme' => get_user_meta( get_current_user_id(), '_wpgp_ace_theme', true ) ) );
	}

	/**
	 * Saves the ACE editor theme to the user meta
	 *
	 * @since     0.4.0
	 */
	public function save_ace_theme() {
		$result = update_user_meta( get_current_user_id(), '_wpgp_ace_theme', $_POST['theme'] );

		if ( ! $result ) {
			wp_send_json_error( array(
				'code'    => 'error',
				'message' => __( 'Failed to update Ace theme.', \WP_Gistpen::$plugin_name ),
			) );
		}

		wp_send_json_success();
	}

	/**
	 * AJAX hook to get JSON of Gistpen IDs missing Gist IDs
	 *
	 * @since 0.5.0
	 */
	public function get_gistpens_missing_gist_id() {
		$result = $this->database->query( 'head' )->missing_gist_id();

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		if ( empty( $result ) ) {
			wp_send_json_error( array(
				'code'    => 'error',
				'message' => __( 'No Gistpens to export.', \WP_Gistpen::$plugin_name ),
			) );
		}

		wp_send_json_success( array( 'ids' => $result ) );
	}

	/**
	 * AJAX hook to trigger export of Gistpen
	 *
	 * @since 0.5.0
	 */
	public function create_gist_from_gistpen_id() {
		$id = intval( $_POST['gistpen_id'] );

		if ( 0 === $id ) {
			wp_send_json_error( array(
				'code'    => 'error',
				'message' => __( 'Invalid Gistpen ID.', \WP_Gistpen::$plugin_name ),
			) );
		}

		$this->database->persist( 'head' )->set_sync( $id, 'on' );
		$result = $this->sync->export_gistpen( $id );

		$this->check_error( $result );

		// This will slow the API exporting process when calling
		// it from the settings page, as the next call
		// won't start until a response has been returned.
		// However, we need to implement a more effective
		// rate limiting, as this API can still receive
		// multiple requests at once and this sleep will
		// do nothing about it.
		sleep( 1 );

		wp_send_json_success( array(
			'code'    => 'success',
			'message' => __( 'Successfully exported Gistpen #', \WP_Gistpen::$plugin_name ) . $result,
		) );
	}

	/**
	 * Get all the Gist IDs for the user from
	 * Gist and check if they've been imported already
	 *
	 * @since 0.5.0
	 */
	public function get_new_user_gists() {
		$gists = $this->gist->get_gists();

		$this->check_error( $gists );

		$new_user_gists = array();

		foreach ( $gists as $gist ) {
			$result = $this->database->query( 'head' )->by_gist_id( $gist );

			if ( empty( $result ) ) {
				$new_user_gists[] = $gist;
			}
		}

		if( empty( $new_user_gists ) ) {
			wp_send_json_error( array(
				'code'    => 'error',
				'message' => __( 'No Gists to import.', \WP_Gistpen::$plugin_name ),
			) );
		}

		wp_send_json_success( array( 'gist_ids' => $new_user_gists ) );
	}

	/**
	 * Import a given Gist ID into the database
	 *
	 * @since 0.5.0
	 */
	public function import_gist() {
		// @todo validate gist ID
		$gist_id = $_POST['gist_id'];

		$result = $this->sync->import_gist( $gist_id );

		$this->check_error( $result );

		wp_send_json_success( array(
			'code'    => 'success',
			'message' => __( 'Successfully imported Gist #', \WP_Gistpen::$plugin_name ) . $gist_id,
		) );
	}
}
