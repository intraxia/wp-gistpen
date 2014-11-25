<?php
namespace WP_Gistpen\Database;

/**
 * @package   WP_Gistpen
 * @author    James DiGioia <jamesorodig@gmail.com>
 * @license   GPL-2.0+
 * @link      http://jamesdigioia.com/wp-gistpen/
 * @copyright 2014 James DiGioia
 */

use WP_Gistpen\Facade\Adapter;

/**
 * This class saves and gets Gistpens from the database
 *
 * @package WP_Gistpen_Query
 * @author  James DiGioia <jamesorodig@gmail.com>
 */
class Query {

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

		$this->args = array(
			'post_type'      => 'gistpen',
			'order'          => 'DESC',
			'orderby'        => 'date',
			'numberposts'    => 5,
			'post_status'    => array( 'publish', 'pending', 'draft', 'future', 'private' )
		);

	}

	public function by_recent() {
		$search_results = get_posts( $this->args );

		if ( empty( $search_results ) ) {
			return $search_results;
		}

		$results = $this->adapter->build_by_array_of_posts( $search_results );

		return $results;
	}

	/**
	 * Search for recent Files
	 *
	 * @param  int|null $search Search term, or null for recent 5
	 * @return array         search results, empty array if no results
	 * @since 0.4.0
	 */
	public function by_string( $search ) {
		$this->args['s'] = $search;

		$search_results = get_posts( $this->args );
		unset($this->args['s']);

		if ( empty( $search_results ) ) {
			return $search_results;
		}

		$results = $this->adapter->build_by_array_of_posts( $search_results );

		return $results;
	}

	public function by_post( $post ) {
		if ( $post->post_type !== 'gistpen' ) {
			return new WP_Error( 'wrong_post_type', __( "WP_Gistpen_Query::get() didn't get a Gistpen", \WP_Gistpen::$plugin_name ) );
		}

		if( 0 !== $post->post_parent ) {
			$result = $this->adapter
				->build( 'file' )
				->by_post( $post );
			$result->set_language( $this->language_by_post_id( $post->ID ) );
		} else {
			$result = $this->adapter
				->build( 'zip' )
				->by_post( $post );
			$result->add_files( $this->files_by_post( $post ) );
		}

		return $result;
	}

	public function by_id( $post_id ) {
		$post = get_post( $post_id );

		return $this->by_post( $post );
	}

	/**
	 * Retrieves the term stdCLass object for a WP_Post object
	 *
	 * @param  WP_Post $post
	 * @return stdClass|WP_Error       term object or Error
	 * @since 0.4.0
	 */
	public function language_by_post_id( $post_id ) {
		$terms = get_the_terms( $post_id, 'wpgp_language' );

		if( empty( $terms ) ) {
			return $this->adapter->build( 'language' )->blank();
		}

		$term = array_pop( $terms );

		return $this->adapter->build( 'language' )->by_slug( $term->slug );
	}

	/**
	 * Retrieves the all the WP_Gistpen_File's for a WP_Post object
	 *
	 * @param  WP_Post $post
	 * @return array|WP_Error       array of WP_Gistpen_Files or Error
	 * @since  0.4.0
	 */
	protected function files_by_post( $post ) {
		$file_posts = get_children( array(
			'post_type' => 'gistpen',
			'post_parent' => $post->ID,
			'post_status' => $post->post_status,
			'order' => 'ASC',
			'orderby' => 'date',
		) );

		if( empty( $file_posts ) ) {
			return $file_posts;
		}

		$files = array();

		foreach ( $file_posts as $file_post ) {
			$file = $this->adapter->build( 'file' )->by_post( $file_post );

			$file->set_language( $this->language_by_post_id( $file_post->ID ) );

			$files[$file_post->ID] = $file;
		}

		return $files;
	}

}
