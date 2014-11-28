<?php
namespace WP_Gistpen\Register;

use WP_Gistpen\Facade\Database;
use WP_Gistpen\Facade\Adapter;

/**
 * This is the functionality for the save_post hook
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Save {

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
	 * Errors codes
	 *
	 * @var string
	 * @since 0.4.0
	 */
	public $errors = '';

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

	}

	/**
	 * save_post action hook callback
	 * to save all the files and
	 * attach them to the Gistpen
	 *
	 * @param  int    $gistpen_id  Gistpen post id
	 * @since  0.4.0
	 */
	public function save_post_hook( $post_id ) {
		// Check user permissions
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id )  ) {
			// @todo save revision children + autosave
			return;
		}

		if ( ! array_key_exists( 'file_ids', $_POST ) ) {
			return;
		}

		$file_ids = explode( ' ', trim( $_POST['file_ids'] ) );

		if ( empty( $file_ids ) ) {
			return;
		}

		$zip = $this->database->query()->by_id( $post_id );

		if ( is_wp_error( $zip ) ) {
			// @todo create ourselves a blank zip
			$zip = $this->adapter->build( 'zip' )->blank();
		}

		foreach ( $file_ids as $file_id ) {

			$files = $zip->get_files();

			if ( array_key_exists( $file_id, $files ) ) {
				$file = $files[ $file_id ];
			} else {
				$file = $this->adapter->build( 'file' )->blank();

				// check if post exists
				if ( get_post_status( $file_id ) ) {
					// we'll use it if it does
					$file->set_ID( $file_id );
				}
			}

			$file_id_w_dash = '-' . $file_id;

			$file->set_slug( $_POST[ 'wp-gistpenfile-slug' . $file_id_w_dash ] );
			$file->set_code( $_POST[ 'wp-gistpenfile-code' . $file_id_w_dash ] );
			$file->set_language( $this->adapter->build( 'language' )->by_slug( $_POST[ 'wp-gistpenfile-language' . $file_id_w_dash ] ) );

			$zip->add_file( $file );

			unset($file);
		}

		remove_action( 'save_post_gistpen', array( $this, 'save_post_hook' ) );

		$result = $this->database->persist()->by_zip( $zip );
		if ( is_wp_error( $result ) ) {
			$this->errors .= $result->get_error_code() . ',';
		}

		add_action( 'save_post_gistpen', array( $this, 'save_post_hook' ) );

		$this->check_errors();
	}

	/**
	 * Check if we need to add errors to the rediect
	 * and add the filter if we do
	 *
	 * @since 0.5.0
	 */
	public function check_errors() {
		if ( $this->errors !== '' ) {
			add_filter( 'redirect_post_location',array( $this, 'return_errors' ) );
		}
	}

	/**
	 * Adds the errors to the url, if any
	 * @param  string $location Current GET params
	 * @return string           Updated GET params
	 */
	public function return_errors( $location ) {
		return add_query_arg( 'gistpen-errors', rtrim( $this->errors, ',' ), $location );
	}

	/**
	 * Deletes the files when a zip gets deleted
	 * @param  int $post_id post ID of the zip being deleted
	 * @since  0.5.0
	 */
	public function delete_post_hook( $post_id ) {
		$zip = $this->database->query()->by_id( $post_id );

		$files = $zip->get_files();

		foreach ( $files as $file ) {
			$result = wp_delete_post( $file->get_ID(), true );

			if ( is_wp_error( $result ) ) {
				$this->errors .= $result->get_error_code() . ',';
			}
		}

		$this->check_errors();
	}
}
