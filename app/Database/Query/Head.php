<?php
namespace Intraxia\Gistpen\Database\Query;

/**
 * @package   Intraxia\Gistpen
 * @author    James DiGioia <jamesorodig@gmail.com>
 * @license   GPL-2.0+
 * @link      http://jamesdigioia.com/wp-gistpen/
 * @copyright 2014 James DiGioia
 */

use Intraxia\Gistpen\Facade\Adapter;
use Intraxia\Gistpen\Model\Zip;
use \WP_Query;
use \WP_Error;

/**
 * This class saves and gets Gistpens from the database
 *
 * @package Intraxia\Gistpen_Query
 * @author  James DiGioia <jamesorodig@gmail.com>
 */
class Head {

	/**
	 * Adapter Facade object
	 *
	 * @var Adapter
	 * @since  0.5.0
	 */
	protected $adapter;

	/**
	 * Default query args
	 *
	 * @var  array
	 * @since 0.5.0
	 */
	protected $args;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.5.0
	 * @var      string    $plugin_name       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct() {
		$this->adapter = new Adapter();

		// Default query args
		$this->args = array(
			'post_type'      => 'gistpen',
			'order'          => 'DESC',
			'orderby'        => 'date',
			'numberposts'    => 5,
			'post_status'    => array( 'publish', 'pending', 'draft', 'future', 'private' ),
		);
	}

	/**
	 * Get the most recently posted Gistpens
	 * This ignores whether they're zips or files
	 *
	 * @return array Array of model objects
	 * @since 0.5.0
	 */
	public function by_recent() {
		$search_results = get_posts( $this->args );

		if ( empty( $search_results ) ) {
			return $search_results;
		}

		$results = $this->by_array_of_posts( $search_results );

		return $results;
	}

	/**
	 * Search for recent Files
	 *
	 * @param  string $search Search term, or null for recent 5
	 * @return array         search results, empty array if no results
	 * @since 0.4.0
	 */
	public function by_string( $search ) {
		$this->args['s'] = $search;

		$search_results = get_posts( $this->args );
		unset( $this->args['s'] );

		if ( empty( $search_results ) ) {
			return $search_results;
		}

		$results = $this->by_array_of_posts( $search_results );

		return $results;
	}

	/**
	 * Turns an array of WP_Post objects into an array of Models
	 * @param  array  $array Array of Posts
	 * @return array         array of Models
	 * @since  0.5.0
	 */
	public function by_array_of_posts( $array ) {
		$results = array();

		foreach ( $array as $post ) {
			$results[] = $this->by_post( $post );
		}

		return $results;
	}

	/**
	 * Gets and builds an object model based on a WP_Post object
	 *
	 * @param  \WP_Post $post model's WP_Post object
	 * @return \Gistpen\Model\Zip|\Gistpen\Model\File
	 * @since 0.5.0
	 */
	public function by_post( $post ) {
		if ( $post->post_type !== 'gistpen' ) {
			return new WP_Error( 'wrong_post_type', __( "Intraxia\Gistpen_Query::get() didn't get a Gistpen", 'wp-gistpen' ) );
		}

		if ( 0 !== $post->post_parent ) {
			$result = $this->adapter
				->build( 'file' )
				->by_post( $post );

			$result->set_language( $this->language_by_post_id( $post->ID ) );
		} else {
			$post->gist_id = $this->gist_id_by_post_id( $post->ID );
			$post->sync = $this->sync_by_post_id( $post->ID );

            $result = new Zip($post);

			$result->set_gist_id( $this->gist_id_by_post_id( $post->ID ) );
			$result->add_files( $this->files_by_post( $post ) );
		}

		return $result;
	}

	/**
	 * Gets and builds an object model based on a post's ID
	 *
	 * @param  int $post_id model's post ID
	 * @return object       Intraxia\Gistpen model object
	 * @since 0.5.0
	 */
	public function by_id( $post_id ) {
		$post = get_post( $post_id );

		return $this->by_post( $post );
	}

	/**
	 * Retrieves the Language object for a given post ID
	 *
	 * @param  int $post_id
	 * @return \Gistpen\Model\Language
	 * @since  0.4.0
	 */
	public function language_by_post_id( $post_id ) {
		$terms = get_the_terms( $post_id, 'wpgp_language' );

		if ( empty( $terms ) ) {
			return $this->adapter->build( 'language' )->blank();
		}

		$term = array_pop( $terms );

		return $this->adapter->build( 'language' )->by_slug( $term->slug );
	}

	/**
	 * Retrieves the Gist ID for a given post ID
	 *
	 * @param  int $post_id
	 * @return string
	 * @since  0.5.0
	 */
	public function gist_id_by_post_id( $post_id ) {
		$gist_id = get_post_meta( $post_id, '_wpgp_gist_id', true );

		if ( empty( $gist_id ) ) {
			$gist_id = 'none';
		}

		return $gist_id;
	}

	/**
	 * Retrieves the sync status for a given post ID
	 *
	 * @param  int $post_id
	 * @return string
	 * @since  0.5.0
	 */
	public function sync_by_post_id( $post_id ) {
		$sync = get_post_meta( $post_id, '_wpgp_sync', true );

		if ( 'on' !== $sync ) {
			$sync = 'off';
		}

		return $sync;
	}

	/**
	 * Retrieves the all the files for a zip's WP_Post object
	 *
	 * @param  \WP_Post $post
	 * @return array       array of Files
	 * @since  0.4.0
	 */
	public function files_by_post( $post ) {
		$file_posts = get_children( array(
			'post_type' => 'gistpen',
			'post_parent' => $post->ID,
			'post_status' => $post->post_status,
			'order' => 'ASC',
			'orderby' => 'date',
		) );

		if ( empty( $file_posts ) ) {
			return $file_posts;
		}

		$files = array();

		foreach ( $file_posts as $file_post ) {
			$file = $this->adapter->build( 'file' )->by_post( $file_post );

			$file->set_language( $this->language_by_post_id( $file_post->ID ) );

			$files[ $file_post->ID ] = $file;
		}

		return $files;
	}

	/**
	 * Gets Gistpen matching given Gist ID
	 * @param  string $gist_id Gist ID to search for
	 * @return \Gistpen\Model\Zip|\WP_error    Zip with given Gist ID, WP_Error if multitple/no Zips match
	 */
	public function by_gist_id( $gist_id ) {
		$query = new WP_Query( array(
			'post_type'        => 'gistpen',
			'post_parent'      => 0,
			'meta_key'         => '_wpgp_gist_id',
			'meta_compare'     => '=',
			'meta_value'       => $gist_id,
			'suppress_filters' => true,
		) );

		$posts = $query->get_posts();

		if ( empty( $posts ) ) {
			return array();
		}

		if ( 1 !== count( $posts ) ) {
			return new WP_Error( 'multiple_gistpens_found', sprintf( __( 'Multiple Gistpens with Gist ID %s found.', 'wp-gistpen' ), $gist_id ) );
		}

		$post = array_pop( $posts );

		return $this->by_post( $post );
	}

	/**
	 * Gets all Gistpens missing Gist IDs
	 *
	 * @return array Zips missing Gist IDs
	 * @since  0.5.0
	 */
	public function missing_gist_id() {
		$query = new WP_Query( array(
			'post_type'        => 'gistpen',
			'order'            => 'ASC',
			'orderby'          => 'date',
			'post_status'      => array( 'publish', 'pending', 'draft', 'future', 'private' ),
			'post_parent'      => 0,
			'meta_key'         => '_wpgp_gist_id',
			'meta_value'       => 'none',
			'meta_compare'     => '=',
			'suppress_filters' => true,
			'nopaging'         => true,
			'fields'           => 'ids',
		));

		return $query->get_posts();
	}
}
