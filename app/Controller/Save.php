<?php
namespace WP_Gistpen\Controller;

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
	 * ID of current ZIP
	 *
	 * @var int
	 * @since 0.5.0
	 */
	private $ID;

	/**
	 * Files array to be added to zip
	 *
	 * @var array
	 * @since 0.5.0
	 */
	private $files;

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
	 *
	 * Checks if it's a Gistpen or a revision
	 * and handles them appropriately.
	 *
	 * @param  int    $gistpen_id  Gistpen post id
	 * @since  0.4.0
	 */
	public function save_post_hook( $post_id ) {
		// Check user permissions
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$this->ID = $post_id;

		remove_action( 'save_post', array( $this, 'save_post_hook' ), 10, 1 );

		if ( 'gistpen' === get_post_type( $this->ID ) ) {
			$this->save_head();

			if ( 'auto-draft' !== get_post_status( $this->ID ) ) {
				$this->save_commit();
			}
		}

		add_action( 'save_post', array( $this, 'save_post_hook' ), 10, 1 );

		$this->check_errors();
	}

	/**
	 * Saves the latest zip data in the main database row
	 * Uses git naming conventions
	 *
	 * @since 0.5.0
	 */
	private function save_head() {
		if ( ! array_key_exists( 'file_ids', $_POST ) ) {
			return;
		}

		$file_ids = explode( ' ', trim( $_POST['file_ids'] ) );
		if ( empty( $file_ids ) ) {
			return;
		}

		$zip = $this->database->query()->by_id( $this->ID );
		if ( ! $zip instanceof \WP_Gistpen\Model\Zip ) {
			return;
		}

		$zip = $this->update_zip_from_post_global( $zip, $file_ids );

		$result = $this->database->persist()->by_zip( $zip );

		$this->add_error( $result );

	}

	/**
	 * Saves a new revision for the current zip data
	 * Uses git naming conventions
	 *
	 * @since 0.5.0
	 */
	private function save_commit() {
		$parent_zip = $this->database->query()->by_id( $this->ID );

		if ( ! $parent_zip instanceof \WP_Gistpen\Model\Zip ) {
			return;
		}

		$result = $this->database->persist( 'commit' )->by_parent_zip( $parent_zip );

		$this->add_error( $result );

	}

	/**
	 * Updates the files array past on the current $_POST data
	 *
	 * @param  \WP_Gistpen\Model\Zip    $zip  object to update
	 * @return \WP_Gistpen\Model\Zip          updated object
	 * @since  0.5.0
	 */
	private function update_zip_from_post_global( $zip, $file_ids ) {
		$this->files = $zip->get_files();

		foreach ( $file_ids as $file_id ) {

			$file = $this->get_file( $file_id );
			$args = $this->get_args( $file_id );

			$file->set_slug( $args['slug'] );
			$file->set_code( $args['code'] );
			$file->set_language( $args['language'] );

			$zip->add_file( $file );

			unset($file);
		}

		return $zip;
	}

	/**
	 * Retrieves a File object based on the file ID
	 *
	 * @param  int                   $file_id post ID of the file
	 * @return WP_Gistpen\Model\File          File model object
	 * @since  0.5.0
	 */
	private function get_file( $file_id ) {
		if ( array_key_exists( $file_id, $this->files ) ) {
			$file = $this->files[ $file_id ];
		} else {
			$file = $this->adapter->build( 'file' )->blank();

			// check if post exists
			if ( get_post_status( $file_id ) ) {
				// we'll use it if it does
				$file->set_ID( $file_id );
			}
		}

		return $file;
	}

	/**
	 * Remove the action hook to save a post revision
	 *
	 * We're going to be handling this ourselves on the save_post hook
	 * @param  int $post_id
	 * @since  0.5.0
	 */
	public function remove_revision_save( $post_id ) {
		if ( 'gistpen' === get_post_type( $post_id ) ) {
			remove_action( 'post_updated', 'wp_save_post_revision', 10 );
		}
	}

	/**
	 * Keeps the File's post_status in sync with
	 * the Zip's post_status
	 *
	 * @param  string $old_status
	 * @param  string $new_status
	 * @param  obj    $post       WP_Post object for zip
	 * @since  0.5.0
	 */
	public function sync_post_status( $new_status, $old_status, $post ) {
		if ( 'gistpen' === $post->post_type && 0 === $post->post_parent && $new_status !== $old_status ) {
			// set to old status so we can query on it
			$post->post_status = $old_status;

			$files = $this->database->query()->files_by_post( $post );

			foreach ( $files as $file ) {
				remove_action( 'save_post', array( $this, 'save_post_hook' ), 10, 1 );
				remove_action( 'transition_post_status', array( $this, 'sync_post_status', 10, 3 ) );

				$result = wp_update_post( array(
					'ID' => $file->get_ID(),
					'post_status' => $new_status,
				), true );

				add_action( 'save_post', array( $this, 'save_post_hook' ), 10, 1 );
				add_action( 'transition_post_status', array( $this, 'sync_post_status' ), 10, 3 );

				$this->add_error( $result );
			}
		}
	}


	/**
	 * Deletes the files when a zip gets deleted
	 *
	 * @param  int $post_id post ID of the zip being deleted
	 * @since  0.5.0
	 */
	public function delete_post_hook( $post_id ) {
		$post = get_post( $post_id );

		if ( 'gistpen' === $post->post_type && 0 === $post->post_parent ) {
			$zip = $this->database->query()->by_post( $post );

			$files = $zip->get_files();

			foreach ( $files as $file ) {
				remove_action( 'delete_post', array( $this, 'delete_post_hook' ), 10, 1 );
				$result = wp_delete_post( $file->get_ID(), true );
				add_action( 'delete_post', array( $this, 'delete_post_hook' ), 10, 1 );

				$this->add_error( $result );
			}

			$this->check_errors();
		}
	}

	/**
	 * Disables checking for changes when we save a post revision
	 *
	 * @param  bool $check_for_changes whether we check for changes
	 * @param  \WP_Post $last_revision     previous revision object
	 * @param  \WP_Post $post              current revision
	 * @return bool                        whether we check for changes
	 * @since  0.5.0
	 */
	public function disable_check_for_change( $check_for_changes, $last_revision, $post ) {
		if ( 'gistpen' === $post->post_type ) {
			$check_for_changes = false;
		}

		return $check_for_changes;
	}

	/**
	 * Retrieves, validates, and organizes the arguments for the File
	 * from the $_POST superglobal
	 *
	 * @param  string $file_id_w_dash
	 * @return array                 Arguments required for manipulating File model object
	 * @since  0.5.0
	 */
	private function get_args( $file_id ) {
		$args = array();

		// @todo validation
		$args['slug'] = $_POST[ 'wp-gistpenfile-slug-' . $file_id ];
		$args['code'] = $_POST[ 'wp-gistpenfile-code-' . $file_id ];
		$args['language'] = $this->adapter->build( 'language' )->by_slug( $_POST[ 'wp-gistpenfile-language-' . $file_id ] );

		return $args;
	}

	/**
	 * Allows empty zip to save
	 *
	 * @param  bool   $maybe_empty Whether post should be considered empty
	 * @param  array  $postarr     Array of post data
	 * @return bool                Result of empty check
	 * @since  0.5.0
	 */
	public function allow_empty_zip( $maybe_empty, $postarr ) {
		if ( 'gistpen' === $postarr['post_type'] && 0 === $postarr['post_parent'] ) {
			$maybe_empty = false;
		}

		return $maybe_empty;
	}

	public function add_error( $result ) {
		if ( is_wp_error( $result ) ) {
			$this->errors .= $result->get_error_code() . ',';
		}
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
	 *
	 * @param  string $location Current GET params
	 * @return string           Updated GET params
	 * @since  0.5.0
	 */
	public function return_errors( $location ) {
		return add_query_arg( 'gistpen-errors', rtrim( $this->errors, ',' ), $location );
	}
}
