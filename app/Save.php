<?php
namespace Intraxia\Gistpen;

use Intraxia\Gistpen\Facade\Adapter;
use Intraxia\Gistpen\Facade\Database;
use Intraxia\Jaxion\Contract\Core\HasActions;
use Intraxia\Jaxion\Contract\Core\HasFilters;

/**
 * This is the functionality for the save_post hook
 *
 * @package    Intraxia\Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Save implements HasActions, HasFilters {
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
	 *
	 * @param Database $database
	 * @param Adapter  $adapter
	 */
	public function __construct( Database $database, Adapter $adapter ) {
		$this->database = $database;
		$this->adapter  = $adapter;
	}

	/**
	 * Remove the action hook to save a post revision
	 *
	 * We're going to be handling this ourselves
	 *
	 * @param  int $post_id
	 *
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
	 * @param  string   $old_status
	 * @param  string   $new_status
	 * @param  \WP_Post $post WP_Post object for zip
	 *
	 * @since  0.5.0
	 */
	public function sync_post_status( $new_status, $old_status, $post ) {
		if ( 'gistpen' === $post->post_type && 0 === $post->post_parent && $new_status !== $old_status ) {
			// set to old status so we can query on it
			$post->post_status = $old_status;

			$files = $this->database->query()->files_by_post( $post );

			foreach ( $files as $file ) {
				wp_update_post( array(
					'ID'          => $file->get_ID(),
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
	 *
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
	 * @param  \WP_Post $last_revision previous revision object
	 * @param  \WP_Post $post current revision
	 *
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
	 * @param  bool  $maybe_empty Whether post should be considered empty.
	 * @param  array $postarr Array of post data.
	 *
	 * @return bool                Result of empty check
	 * @since  0.5.0
	 */
	public function allow_empty_zip( $maybe_empty, $postarr ) {
		if ( 'gistpen' === $postarr['post_type'] && 0 === $postarr['post_parent'] ) {
			$maybe_empty = false;
		}

		return $maybe_empty;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return array[]
	 */
	public function action_hooks() {
		return array(
			array(
				'hook'     => 'post_updated',
				'method'   => 'remove_revision_save',
				'priority' => 9,
			),
			array(
				'hook'   => 'transition_post_status',
				'method' => 'sync_post_status',
				'args'   => 3,
			),
			array(
				'hook'   => 'before_delete_post',
				'method' => 'delete_files',
			),
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return array[]
	 */
	public function filter_hooks() {
		return array(
			array(
				'hook'   => 'wp_insert_post_empty_content',
				'method' => 'allow_empty_zip',
				'args'   => 2,
			),
			array(
				'hook'   => 'wp_save_post_revision_check_for_changes',
				'method' => 'disable_check_for_change',
				'args'   => 3,
			),
		);
	}
}
