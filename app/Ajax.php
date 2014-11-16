<?php
namespace WP_Gistpen;

use \WP_Post;
use WP_Gistpen\Database\Query;
use WP_Gistpen\Model\Zip;
use WP_Gistpen\Model\File;
use WP_Gistpen\Model\Language;

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

		$search = null;

		if ( isset( $_POST['gistpen_search_term'] ) ) {
			$search = $_POST['gistpen_search_term'];
		}
		$results = Query::search( $search );

		if ( is_wp_error( $results ) ) {
			wp_send_json_error( array( 'error' => $results->get_error_message() ) );
		}

		$data = array( 'gistpens' => $results );

		wp_send_json_success( $data );
	}

	/**
	 * Responds to AJAX request to create new Gistpen
	 *
	 * @return string $post_id the id of the created Gistpen
	 * @since  0.2.0
	 */
	public function create_gistpen() {
		$this->check_security();

		$post_data = new WP_Post( new \stdClass );
		$post_data->post_type = 'gistpen';
		$post_data->post_status = $_POST['post_status'];

		$result = Query::create( $post_data );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_messages() ) );
		}

		$result->description = $_POST['wp-gistfile-description'];

		$file = new File( new WP_Post( new \stdClass ), new Language( new \stdClass  ) );
		$file->slug = $_POST['wp-gistpenfile-slug'];
		$file->code = $_POST['wp-gistpenfile-code'];
		$file->language->slug = $_POST['wp-gistpenfile-language'];

		$result->files[] = $file;

		$result = Query::save( $result );

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

		$file = new \stdCLass;
		$file->post_type = 'gistpen';
		$file->post_parent = $_POST['parent_id'];
		$file->post_status = 'auto-draft';

		$file = new WP_Post( $file );
		$language = new \stdCLass;
		$language->slug = 'bash';
		$file = new File( $file, new Language( $language ) );

		$result = Query::save( $file );

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
