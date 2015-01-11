<?php
namespace WP_Gistpen\Database\Query;

/**
 * @package   WP_Gistpen
 * @author    James DiGioia <jamesorodig@gmail.com>
 * @license   GPL-2.0+
 * @link      http://jamesdigioia.com/wp-gistpen/
 * @copyright 2014 James DiGioia
 */

use WP_Gistpen\Collection\History;
use WP_Gistpen\Database\Query\Head as HeadQuery;
use WP_Gistpen\Facade\Adapter;
use WP_Gistpen\Model\Commit as CommitModel;

/**
 * This class saves and gets Gistpen commits from the database
 *
 * @package WP_Gistpen_Query
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
	 * Adapter Facade object
	 *
	 * @var Adapter
	 * @since  0.5.0
	 */
	private $adapter;

	/**
	 * HeadQuery object
	 *
	 * @var HeadQuery
	 * @since 0.5.0
	 */
	protected $head_query;

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

	}

	/**
	 * Gets and builds a History Collection for a given post ID
	 *
	 * @param  int     $post_id model's post ID
	 * @return History          WP_Gistpen model object
	 * @since 0.5.0
	 */
	public function history_by_head_id( $head_id ) {
		$history = $this->adapter->build( 'history' )->blank();
		$revisions = wp_get_post_revisions( $head_id );

		foreach ( $revisions as $revision ) {
			$commit = $this->by_post( $revision );

			$history->add_commit( $commit );
		}

		return $history;
	}

	/**
	 * Get the latest Commit based on the Head ID
	 *
	 * @param  int    $head_id ID of the Head Zip to query on
	 * @return Commit          Latest Commit object
	 */
	public function latest_by_head_id( $head_id ) {
		$revisions = wp_get_post_revisions( $head_id, array( 'posts_per_page' => 1 ) );
		$revision = array_shift( $revisions );

		return $this->by_post( $revision );
	}

	/**
	 * Gets and builds a Commit model based on a WP_Post object
	 *
	 * @param  WP_Post $post model's WP_Post object
	 * @return object       Commit model object
	 * @since 0.5.0
	 */
	public function by_post( $post ) {
		// @todo validate this post so it's a revision & pulling from a Zip
		$post->post_status = get_post_status( $post->post_parent );
		$commit = $this->adapter->build( 'commit' )->by_post( $post );

		$commit->set_head_gist_id( $this->head_query->gist_id_by_post_id( $commit->get_head_id() ) );
		$commit->set_sync( $this->head_query->sync_by_post_id( $commit->get_head_id() ) );

		$meta = get_metadata( 'post', $commit->get_ID(), '_wpgp_commit_meta', true );

		foreach ( $meta['state_ids'] as $state_id ) {
			$state = $this->state_by_id( $state_id );

			$commit->add_state( $state );
		}

		if ( array_key_exists( 'gist_id', $meta ) ) {
			$commit->set_gist_id( $meta['gist_id'] );
		}

		return $commit;
	}

	/**
	 * Gets and builds an object model based on a post's ID
	 *
	 * @param  int $post_id model's post ID
	 * @return object       WP_Gistpen model object
	 * @since 0.5.0
	 */
	public function by_id( $post_id ) {
		$revision = get_post( $post_id );

		return $this->by_post( $revision );
	}

	/**
	 * Get a State object by the State's ID and its Commit ID
	 *
	 * @param  int    $state_id  ID of the State
	 * @param  int    $commit_id ID of the State's Commit
	 * @return State             State object
	 * @since  0.5.0
	 */
	public function state_by_id( $state_id ) {
		$state_post = get_post( $state_id );
		$meta = get_metadata( 'post', $state_id, "_wpgp_state_meta", true );

		if ( empty( $meta ) || ! array_key_exists( 'status', $meta ) ) {
			$state_post->status = 'new';
		} else {
			$state_post->status = $meta['status'];
		}

		if ( 'new' !== $state_post->status ) {
			$state_post->gist_id = $meta['gist_id'];
		}

		$state = $this->adapter->build( 'state' )->by_post( $state_post );

		$state->set_language( $this->language_by_state_id( $state_id ) );

		return $state;
	}

	/**
	 * Retrieves the Language object for a given State ID
	 *
	 * @param  int $post_id
	 * @return Language
	 * @since  0.4.0
	 */
	public function language_by_state_id( $state_id ) {
		$terms = get_the_terms( $state_id, 'wpgp_language' );

		if ( empty( $terms ) ) {
			return $this->adapter->build( 'language' )->blank();
		}

		$term = array_pop( $terms );

		return $this->adapter->build( 'language' )->by_slug( $term->slug );
	}
}
