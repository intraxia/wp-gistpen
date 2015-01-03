<?php
namespace WP_Gistpen\Database\Persistance;
/**
 * @package   WP_Gistpen
 * @author    James DiGioia <jamesorodig@gmail.com>
 * @license   GPL-2.0+
 * @link      http://jamesdigioia.com/wp-gistpen/
 * @copyright 2014 James DiGioia
 */

use WP_Gistpen\Database\Query\Head as HeadQuery;
use WP_Gistpen\Database\Query\Commit as CommitQuery;
use WP_Gistpen\Facade\Adapter;
use WP_Gistpen\Model\File;
use WP_Gistpen\Model\Language;

/**
 * This class manipulates the saving of parent Gistpen
 * and all child Gistpens.
 *
 * @package Commit
 * @author  James DiGioia <jamesorodig@gmail.com>
 */
class Commit {

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
	 * Adapter facade
	 *
	 * @var Adapter
	 */
	private $adapter;

	/**
	 * Database object for querying Head
	 *
	 * @var HeadQuery
	 */
	private $head_query;

	/**
	 * Database object for querying Commit
	 *
	 * @var CommitQuery
	 */
	private $commit_query;

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

		$this->adapter = new Adapter( $plugin_name, $version );
		$this->head_query = new HeadQuery( $plugin_name, $version );
		$this->commit_query = new CommitQuery( $plugin_name, $version );

	}

	/**
	 * Save the data for a new commit to the database
	 *
	 * @param  Array           $commit Zip model of the parent to save
	 * @return array|\WP_Error         revision meta saved, WP_Error if failed
	 * @since  0.5.0
	 */
	public function by_ids( $ids ) {
		// @todo validate $ids?
		$revisions = wp_get_post_revisions( $ids['zip'] );

		if ( ! empty( $revisions ) ) {
			$prev_revision = array_shift( $revisions );
			$prev_commit_id = $prev_revision->ID;
			unset( $prev_revision );
			unset( $revisions );
		}

		$commit_id = wp_save_post_revision( $ids['zip'] );

		// @todo error checking

		$commit_meta = array();

		foreach ( $ids['files'] as $file_id ) {
			$state_meta = array();

			// check if previous revision(s) exists
			$file_revisions = wp_get_post_revisions( $file_id );

			$state_id = wp_save_post_revision( $file_id );

			if ( empty( $file_revisions ) ) {
				// if not, set status to 'new'
				$state_meta['status'] = 'new';
			} else {
				// if so, set status to 'updated'
				$state_meta['status'] = 'updated';

				// Set prev filename as current gist_id
				$prev_revision = array_shift( $file_revisions );
				$prev_state = $this->commit_query->state_by_id( $prev_revision->ID, $prev_commit_id );
				$state_meta['gist_id'] = $prev_state->get_filename();

				if ( empty( $state_id ) ) {
					// if we fail to save a revision, it's because there was no change
					// so we use the state ID of the previous commit
					$state_id = $prev_state->get_ID();
				}
			}

			update_metadata( 'post', $state_id, "_wpgp_{$commit_id}_state_meta", $state_meta );

			$lang_slug = $this->head_query->language_by_post_id( $file_id )->get_slug();

			wp_set_object_terms( $state_id, $lang_slug, 'wpgp_language', false );

			$commit_meta['state_ids'][] = $state_id;
		}

		if ( array_key_exists( 'deleted', $ids ) ) {
			foreach ( $ids['deleted'] as $deleted_file_id ) {
				$file_revisions = wp_get_post_revisions( $deleted_file_id );
				$prev_revision = array_shift( $file_revisions );
				$prev_state = $this->commit_query->state_by_id( $prev_revision->ID, $prev_commit_id );

				$state_meta = array(
					'status'  => 'deleted',
					'gist_id' => $prev_state->get_filename(),
				);

				wp_update_post( array(
					'ID'          => $deleted_file_id,
					'post_type'   => 'revision',
					'post_status' => 'inherit',
					'post_parent' => $commit_id,
				) );

				update_metadata( 'post', $deleted_file_id, "_wpgp_{$commit_id}_state_meta", $state_meta );

				$commit_meta['state_ids'][] = $deleted_file_id;
			}
		}

		update_metadata( 'post', $commit_id, '_wpgp_commit_meta', $commit_meta );

		return $commit_id;
	}

	/**
	 * Save a Gist ID to the Commit
	 *
	 * @param  int    $commit_id  post ID of Commit to update
	 * @param  string $gist_id    Gist ID to save
	 * @since  0.5.0
	 */
	public function set_gist_id( $commit_id, $gist_id ) {
		$meta = get_metadata( 'post', $commit_id, '_wpgp_commit_meta', true );
		$meta['gist_id'] = $gist_id;

		return update_metadata( 'post', $commit_id, '_wpgp_commit_meta', $meta );
	}
}
