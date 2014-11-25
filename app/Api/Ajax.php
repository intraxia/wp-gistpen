<?php
namespace WP_Gistpen\Api;

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
	 * Slug for the nonce field
	 *
	 * @var string
	 * @since  0.4.0
	 */
	private $nonce_field;

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
		$this->nonce_field = '_ajax_wp_gistpen';

		$this->database = new Database( $plugin_name, $version );
		$this->adapter = new Adapter( $plugin_name, $version );

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
	 * @return Sends error and halts execution if anything doesn't check out
	 * @since  0.4.0
	 */
	public function check_security() {
		// Check the nonce
		if ( ! isset($_POST['nonce']) || ! wp_verify_nonce( $_POST['nonce'], $this->nonce_field ) ) {
			wp_send_json_error( array( 'error' => __( "Nonce check failed.", $this->plugin_name ) ) );
		}

		// Check if user has proper permisissions
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( array( 'error' => __( "User doesn't have proper permisissions.", $this->plugin_name ) ) );
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

		if ( isset( $_POST['gistpen_search_term'] ) ) {
			$results = $this->database->query()->by_string( $_POST['gistpen_search_term'] );
		} else {
			$results = $this->database->query()->by_recent();
		}

		wp_send_json_success( array(
			'gistpens' => $results
		) );
	}

	/**
	 * Responds to AJAX request to create new Gistpen
	 *
	 * @return string $post_id the id of the created Gistpen
	 * @since  0.2.0
	 */
	public function create_gistpen() {
		$this->check_security();

		$data = array(
			'description' => $_POST['wp-gistpenfile-language'],
			'status'      => $_POST['post_status'],
			'files'       => array(
				array(
					'slug' => $_POST['wp-gistpenfile-slug'],
					'code' => $_POST['wp-gistpenfile-code'],
					'language' => $_POST['wp-gistpenfile-language']
				)
			)
		);

		$result = $this->database->persist()->by_zip( $this->adapter->build( 'zip' )->by_array( $data ) );

		if( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		wp_send_json_success( array( 'id' => $result ) );
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
			wp_send_json_error();
		}

		wp_send_json_success();
	}


	/**
	 * AJAX hook to get a new ACE editor
	 *
	 * @since     0.4.0
	 */
	public function get_gistpenfile_id() {
		$this->check_security();

		if( ! array_key_exists('parent_id', $_POST ) ) {
			wp_send_json_error( array( 'messages' => array( 'Parent ID not sent.' ) ) );
		}

		$result = $this->database->persist->by_file_and_zip_id( $this->adapter->build( 'file' )->blank(), $_POST['parent_id'] );

		if( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		wp_send_json_success( array( 'id' => $result ) );
	}

	/**
	 * AJAX hook to delete an ACE editor
	 *
	 * @since     0.4.0
	 */
	public function delete_gistpenfile() {
		$this->check_security();

		if( ! array_key_exists('fileID', $_POST ) ) {
			wp_send_json_error( array( 'messages' => array( 'File ID not sent.' ) ) );
		}

		$result = wp_delete_post( $_POST['fileID'], true );

		if( ! $result ) {
			wp_send_json_error( array( 'message' => __( 'wp_delete_post failed', $this->plugin_name ) ) );
		}

		wp_send_json_success();
	}
}
