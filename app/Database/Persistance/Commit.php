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
	 * @since    0.5.0
	 */
	private $adapter;

	/**
	 * Database object for querying Head
	 *
	 * @var HeadQuery
	 * @since    0.5.0
	 */
	private $head_query;

	/**
	 * Database object for querying Commit
	 *
	 * @var CommitQuery
	 * @since    0.5.0
	 */
	private $commit_query;

	/**
	 * Whether the current commit changed
	 *
	 * @var boolean
	 * @since    0.5.0
	 */
	protected $changed = false;

	/**
	 * Commit meta for the current commit
	 *
	 * @var   array
	 * @since 0.5.0
	 */
	protected $commit_meta = array( 'state_ids' => array() );

	/**
	 * ID for current commit
	 *
	 * @var integer
	 * @since    0.5.0
	 */
	protected $commit_id = 0;

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
		// reset object defaults
		$this->changed = false;
		$this->commit_meta = array( 'state_ids' => array() );
		$this->commit_id = 0;
		$this->ids = $ids;

		if ( ! array_key_exists( 'zip', $this->ids ) || ! is_integer( $this->ids['zip'] ) ) {
			// @todo add message
			return new \WP_Error();
		}

		if ( array_key_exists( 'files', $this->ids ) && is_array( $this->ids['files'] ) ) {
			$this->states_by_file_ids( $this->ids['files'] );
		}

		if ( array_key_exists('deleted', $this->ids ) && is_array( $this->ids['deleted'] ) ) {
			$this->deleted_states_by_file_ids( $this->ids['deleted'] );
		}

		// If files haven't changed...
		if ( ! $this->changed ) {
			// ...and if no states are connected...
			if ( empty( $this->commit_meta['state_ids'] ) ) {
				// ...then we're done.
				return $this->ids;
			}

			$revisions = wp_get_post_revisions( $this->ids['zip'] );

			// ...and there are no commits...
			if ( empty( $revisions ) ) {
				// ...save a commit...
				$this->commit_id = wp_save_post_revision( $this->ids['zip'] );
			}
		// ...but if files have changed and no commit was saved...
		} elseif ( 0 === $this->commit_id ) {
			// ...save a commit...
			$this->commit_id = wp_save_post_revision( $this->ids['zip'] );
		}

		// ...and if no commit has been saved...
		if ( 0 === $this->commit_id ) {
			$prev_revision = array_shift( $revisions );
			$current_post = get_post( $this->ids['zip'] );

			// ...and if the description has changed...
			if ( normalize_whitespace( $current_post->post_title ) !== normalize_whitespace( $prev_revision->post_title ) ) {
				// ...save a commit...
				$this->commit_id = wp_save_post_revision( $this->ids['zip'] );
			}
		}

		// ...and if a commit has been saved...
		if ( 0 !== $this->commit_id ) {
			// ...save the commit meta.
			update_metadata( 'post', $this->commit_id, '_wpgp_commit_meta', $this->commit_meta );
		}

		return $this->ids;
	}

	/**
	 * Saves new States for array of Files IDs
	 *
	 * @param  array $file_ids Array of File IDs
	 * @return bool            whether this saved new files
	 * @since  0.5.0
	 */
	protected function states_by_file_ids( $file_ids ) {
		$changed = false;
		foreach ( $file_ids as $file_id ) {
			if ( ! is_integer( $file_id ) ) {
				continue;
			}

			$state_id = wp_save_post_revision( $file_id );
			$revisions = wp_get_post_revisions( $file_id );

			if ( null === $state_id || 0 === $state_id ) {
				$prev_revision = array_shift( $revisions );
				$state_id = $prev_revision->ID;

				$this->commit_meta['state_ids'][] = $state_id;
				continue;
			}

			$state_meta = array();

			// Removing the latest revision first
			array_shift( $revisions );

			if ( empty( $revisions ) ) {
				// if not, set status to 'new'
				$state_meta['status'] = 'new';
			} else {
				// if so, set status to 'updated'
				$state_meta['status'] = 'updated';

				// Set prev filename as current gist_id
				$prev_revision = array_shift( $revisions );
				$prev_state = $this->commit_query->state_by_id( $prev_revision->ID );
				$state_meta['gist_id'] = $prev_state->get_filename();
			}

			update_metadata( 'post', $state_id, "_wpgp_state_meta", $state_meta );

			$lang_slug = $this->head_query->language_by_post_id( $file_id )->get_slug();

			wp_set_object_terms( $state_id, $lang_slug, 'wpgp_language', false );

			$this->changed = true;
			$changed = true;

			$this->commit_meta['state_ids'][] = $state_id;
		}

		return $changed;
	}

	/**
	 * Sets files as deleted by array of File IDs
	 *
	 * @param  array $file_ids Array of File IDs
	 * @return bool            whether this saved new files
	 * @since  0.5.0
	 */
	public function deleted_states_by_file_ids( $file_ids ) {
		$changed = false;
		foreach ( $file_ids as $file_id ) {
			if ( ! is_integer( $file_id ) ) {
				continue;
			}

			if ( 0 === $this->commit_id ) {
				$this->commit_id = wp_save_post_revision( $this->ids['zip'] );
			}

			$revisions = wp_get_post_revisions( $file_id );
			$prev_revision = array_shift( $revisions );
			$prev_state = $this->commit_query->state_by_id( $prev_revision->ID );

			$state_meta = array(
				'status'  => 'deleted',
				'gist_id' => $prev_state->get_filename(),
			);

			wp_update_post( array(
				'ID'          => $file_id,
				'post_type'   => 'revision',
				'post_status' => 'inherit',
				'post_parent' => $this->commit_id,
			) );

			update_metadata( 'post', $file_id, "_wpgp_state_meta", $state_meta );

			$this->commit_meta['state_ids'][] = $file_id;
		}
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
