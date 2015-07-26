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
	protected $database;

	/**
	 * Adapter Facade object
	 *
	 * @var Adapter
	 * @since  0.5.0
	 */
	protected $adapter;

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
	 * Update a Zip and save a new revision
	 *
	 * @param  array  $zip_data  Array of zip data
	 * @return int|\WP_Error      Zip ID on success, WP_Error on failure
	 * @since  0.5.0
	 */
	public function update( $zip_data ) {
		if ( 'auto-draft' === $zip_data['status'] ) {
			$zip_data['status'] = 'draft';
		}

		$zip = $this->adapter->build( 'zip' )->by_array( $zip_data );

		// Check user permissions
		if ($zip->get_ID()) {
			if ( ! current_user_can( 'edit_post', $zip->get_ID() ) ) {
				return new \WP_Error( 'no_perms', __( 'User does not have permission to edit post ', \WP_Gistpen::$plugin_name ) . $zip->get_ID() );
			}
		} else {
			if ( ! current_user_can( 'edit_posts' ) ) {
				return new \WP_Error( 'no_perms', __( 'User does not have permission to edit post ', \WP_Gistpen::$plugin_name ) . $zip->get_ID() );
			}
		}

		foreach ( $zip_data['files'] as $file_data ) {
			$file = $this->adapter->build( 'file' )->by_array( $file_data );
			$file->set_language( $this->adapter->build( 'language' )->by_slug( $file_data['language'] ) );

			$zip->add_file( $file );
			unset( $file );
		}

		$results = $this->database->persist( 'head' )->by_zip( $zip );

		if ( is_wp_error( $results ) ) {
			return $results;
		}

		$result = $this->database->persist( 'commit' )->by_ids( $results );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		/**
		 * After Update filter hook.
		 *
		 * Can hook in and return WP_Error objects
		 * if something happens that results in an error.
		 * This response is important when communicating
		 * with GitHub's API. If something goes wrong,
		 * we want to return that error to the user
		 * so they can go fix it. This response is returned
		 * by the Ajax API.
		 */
		return apply_filters( 'wpgp_after_update', $results['zip'] );
	}

	/**
	 * Remove the action hook to save a post revision
	 *
	 * We're going to be handling this ourselves
	 *
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
				wp_update_post( array(
					'ID' => $file->get_ID(),
					'post_status' => $new_status,
				), true );
			}

			do_action( 'wpgp_after_status_update', $new_status, $old_status, $post->ID );
		}
	}

	/**
	 * Deletes the files when a zip gets deleted
	 *
	 * @param  int $post_id post ID of the zip being deleted
	 * @since  0.5.0
	 */
	public function delete_files( $post_id ) {
		$post = get_post( $post_id );

		if ( 'gistpen' === $post->post_type && 0 === $post->post_parent ) {
			$zip = $this->database->query()->by_post( $post );

			$files = $zip->get_files();

			foreach ( $files as $file ) {
				wp_delete_post( $file->get_ID(), true );
			}

			do_action( 'wpgp_after_delete', $zip );
		}
	}

	/**
	 * Disables checking for changes when we save a post revision
	 *
	 * @param  bool     $check_for_changes whether we check for changes
	 * @param  \WP_Post $last_revision     previous revision object
	 * @param  \WP_Post $post              current revision
	 * @return bool                        whether we check for changes
	 * @since  0.5.0
	 */
	public function disable_check_for_change( $check_for_changes, $last_revision, $post ) {
		if ( 'gistpen' === $post->post_type && 0 === $post->post_parent ) {
			$check_for_changes = false;
		}

		return $check_for_changes;
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
}
