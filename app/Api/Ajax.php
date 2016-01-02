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
	 * Checks if the result is a WP_Error object
	 *
	 * @param  mixed|\WP_Error $result
	 * @since  0.5.0
	 */
	private function check_error( $result ) {
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array(
				'code'    => 'error',
				'message' => $result->get_error_message(),
			) );
		}
	}

	/**
	 * Returns the data for a single Gistpen
	 *
	 * @since 0.5.0
	 */
	public function get_gistpen() {
		if ( ! array_key_exists( 'post_id', $_POST ) ) {
			wp_send_json_error( array(
				'code'    => 'error',
				'message' => __( 'No Gistpen ID sent', \WP_Gistpen::$plugin_name ),
			) );
		}

		$post_id = (int) $_POST['post_id'];

		$zip = $this->database->query( 'head' )->by_id( $post_id );

		$this->check_error( $zip );

		$zip_json = $this->adapter->build( 'api' )->by_zip( $zip );

		wp_send_json_success( $zip_json );
	}

	/**
	 * Responds to AJAX request to create new Gistpen
	 *
	 * @since  0.2.0
	 */
	public function create_gistpen() {
		$zip_data = array(
			'description' => $_POST['wp-gistfile-description'],
			'status'      => $_POST['post_status'],
		);
		$zip = $this->adapter->build( 'zip' )->by_array( $zip_data );

		$file_data = array(
			'slug' => $_POST['wp-gistpenfile-slug'],
			'code' => $_POST['wp-gistpenfile-code'],
		);
		$file = $this->adapter->build( 'file' )->by_array( $file_data );

		$language = $this->adapter->build( 'language' )->by_slug( $_POST['wp-gistpenfile-language'] );
		$file->set_language( $language );

		$zip->add_file( $file );

		$result = $this->database->persist()->by_zip( $zip );

		$this->check_error( $result );

		wp_send_json_success( array( 'id' => $result['zip'] ) );
	}

	/**
	 * AJAX hook to save Gistpen in the editor
	 *
	 * @since 0.5.0
	 */
	public function save_gistpen() {
		// @todo validate data
		$zip_data = $_POST['zip'];

		$result = $this->save->update( $zip_data );

		$this->check_error( $result );

		wp_send_json_success( array(
			'code'    => 'updated',
			'message' => __( 'Successfully updated Gistpen ', \WP_Gistpen::$plugin_name ) . $result,
		) );
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
