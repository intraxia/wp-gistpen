<?php
namespace Intraxia\Gistpen\Api;

use Intraxia\Gistpen\Client\Gist;
use Intraxia\Gistpen\Controller\Save;
use Intraxia\Gistpen\Controller\Sync;
use Intraxia\Gistpen\Facade\Database;
use Intraxia\Gistpen\Facade\Adapter;
use Intraxia\Gistpen\Model\Zip;

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
	 * Actions hooks for AJAX service
	 *
	 * @var array
	 */
	public $actions = array(
		array(
			'hook' => 'edit_form_after_title',
			'method' => 'embed_nonce',
		),
		array(
			'hook' => 'wpgp_settings_before_title',
			'method' => 'embed_nonce',
		),
		array(
			'hook' => 'wp_ajax_get_gistpens',
			'method' => 'get_gistpens',
		),
		array(
			'hook' => 'wp_ajax_get_gistpen',
			'method' => 'get_gistpen',
		),
		array(
			'hook' => 'wp_ajax_create_gistpen',
			'method' => 'create_gistpen',
		),
		array(
			'hook' => 'wp_ajax_save_gistpen',
			'method' => 'save_gistpen',
		),
		array(
			'hook' => 'wp_ajax_save_ace_theme',
			'method' => 'save_ace_theme',
		),
		array(
			'hook' => 'wp_ajax_get_ace_theme',
			'method' => 'get_ace_theme',
		),
		array(
			'hook' => 'wp_ajax_get_gistpens_missing_gist_id',
			'method' => 'get_gistpens_missing_gist_id',
		),
		array(
			'hook' => 'wp_ajax_create_gist_from_gistpen_id',
			'method' => 'create_gist_from_gistpen_id',
		),
		array(
			'hook' => 'wp_ajax_get_new_user_gists',
			'method' => 'get_new_user_gists',
		),
		array(
			'hook' => 'wp_ajax_import_gist',
			'method' => 'import_gist',
		),
	);

    /**
     * Initialize the class and set its properties.
     *
     * @since    0.5.0
     *
     * @param Sync $sync
     * @param Gist $gist
     */
    public function __construct(Sync $sync, Gist $gist)
    {
		$this->nonce_field = '_ajax_wp_gistpen';

        $this->database = new Database();
        $this->adapter = new Adapter();

        $this->save = new Save();
        $this->sync = $sync;
        $this->gist = $gist;
    }

	/**
	 * Embed the nonce in the head of the editor
	 *
	 * @return string    AJAX nonce
	 * @since  0.2.0
	 */
	public function embed_nonce() {
		wp_nonce_field( $this->nonce_field, $this->nonce_field, false );
	}

	/**
	 * Checks nonce and user permissions for AJAX reqs
	 *
	 * @since  0.4.0
	 */
	private function check_security() {
		// Check the nonce
		if ( ! isset($_POST['nonce']) || ! wp_verify_nonce( $_POST['nonce'], $this->nonce_field ) ) {
			wp_send_json_error( array(
				'code'    => 'error',
				'message' => __( 'Nonce check failed.', 'wp-gistpen' ),
			) );
		}

		// Check if user has proper permisissions
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( array(
				'code'    => 'error',
				'message' => __( "User doesn't have proper permisissions.", 'wp-gistpen' ),
			) );
		}
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
	 * Returns 5 most recent Gistpens
	 * or Gistpens matching search term
	 *
	 * @return string JSON-encoded array of post objects
	 * @since 0.4.0
	 */
	public function get_gistpens() {
		$this->check_security();

		if ( isset( $_POST['gistpen_search_term'] ) && ! empty( $_POST['gistpen_search_term'] ) ) {
			$results = $this->database->query()->by_string( $_POST['gistpen_search_term'] );
		} else {
			$results = $this->database->query()->by_recent();
		}

		$this->check_error( $results );

		$results = $this->adapter->build( 'api' )->by_array_of_models( $results );

		wp_send_json_success( array(
			'gistpens' => $results,
		) );
	}

	/**
	 * Returns the data for a single Gistpen
	 *
	 * @since 0.5.0
	 */
	public function get_gistpen() {
		$this->check_security();

		if ( ! array_key_exists( 'post_id', $_POST ) ) {
			wp_send_json_error( array(
				'code'    => 'error',
				'message' => __( 'No Gistpen ID sent', 'wp-gistpen' ),
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
		$this->check_security();

		$zip_data = array(
			'description' => $_POST['wp-gistfile-description'],
			'status'      => $_POST['post_status'],
		);
        $zip = new Zip($zip_data);

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
		$this->check_security();

		// @todo validate data
		$zip_data = $_POST['zip'];

		$result = $this->save->update( $zip_data );

		$this->check_error( $result );

		wp_send_json_success( array(
			'code'    => 'updated',
			'message' => __( 'Successfully updated Gistpen ', 'wp-gistpen' ) . $result,
		) );
	}

	/**
	 * Retrieves the ACE editor theme from the user meta
	 *
	 * @since 0.5.0
	 */
	public function get_ace_theme() {
		$this->check_security();

		wp_send_json_success( array( 'theme' => get_user_meta( get_current_user_id(), '_wpgp_ace_theme', true ) ) );
	}

	/**
	 * Saves the ACE editor theme to the user meta
	 *
	 * @since     0.4.0
	 */
	public function save_ace_theme() {
		$this->check_security();

		$result = update_user_meta( get_current_user_id(), '_wpgp_ace_theme', $_POST['theme'] );

		if ( ! $result ) {
			wp_send_json_error( array(
				'code'    => 'error',
				'message' => __( 'Failed to update Ace theme.', 'wp-gistpen' ),
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
		$this->check_security();

		$result = $this->database->query( 'head' )->missing_gist_id();

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		if ( empty( $result ) ) {
			wp_send_json_error( array(
				'code'    => 'error',
				'message' => __( 'No Gistpens to export.', 'wp-gistpen' ),
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
		$this->check_security();

		$id = intval( $_POST['gistpen_id'] );

		if ( 0 === $id ) {
			wp_send_json_error( array(
				'code'    => 'error',
				'message' => __( 'Invalid Gistpen ID.', 'wp-gistpen' ),
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
			'message' => __( 'Successfully exported Gistpen #', 'wp-gistpen' ) . $result,
		) );
	}

	/**
	 * Get all the Gist IDs for the user from
	 * Gist and check if they've been imported already
	 *
	 * @since 0.5.0
	 */
	public function get_new_user_gists() {
		$this->check_security();

		$gists = $this->gist->all();

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
				'message' => __( 'No Gists to import.', 'wp-gistpen' ),
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
		$this->check_security();

		// @todo validate gist ID
		$gist_id = $_POST['gist_id'];

		$result = $this->sync->import_gist( $gist_id );

		$this->check_error( $result );

		wp_send_json_success( array(
			'code'    => 'success',
			'message' => __( 'Successfully imported Gist #', 'wp-gistpen' ) . $gist_id,
		) );
	}
}
