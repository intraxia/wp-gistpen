<?php
namespace WP_Gistpen\View;

/**
 * Registers the front-end content output
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */

use WP_Gistpen\Facade\Database;

/**
 * This class manipulates the Gistpen post content.
 *
 * @package Content
 * @author  James DiGioia <jamesorodig@gmail.com>
 */
class Content {

	/**
	 * Database Facade object
	 *
	 * @var Database
	 * @since 0.5.0
	 */
	protected $database;

	/**
	 * Action hooks for Content service
	 *
	 * @var array
	 */
	public $actions = array(
		array(
			'hook' => 'the_content',
			'method' => 'remove_filters',
		),
	);

	/**
	 * Filter hooks for Content service
	 *
	 * @var array
	 */
	public $filters = array(
		array(
			'hook' => 'the_content',
			'method' => 'post_content',
		),
		array(
			'hook' => 'pre_get_posts',
			'method' => 'pre_get_posts',
		),
	);

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.5.0
	 * @var      string    $plugin_name       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct() {
		$this->database = new Database();
	}

	/**
	 * Remove extra filters from the Gistpen content
	 *
	 * @param string $content
	 * @return string
	 * @since    0.1.0
	 */
	public function remove_filters( $content ) {
		if ( 'gistpen' === get_post_type() ) {
			remove_filter( 'the_content', 'wpautop' );
			remove_filter( 'the_content', 'wptexturize' );
			remove_filter( 'the_content', 'capital_P_dangit' );
			remove_filter( 'the_content', 'convert_chars' );
			remove_filter( 'get_the_excerpt', 'wp_trim_excerpt' );
		}

		return $content;
	}

	/**
	 * Add the Gistpen content field to the_content
	 *
	 * @param string $content
	 * @return string post_content
	 * @since    0.1.0
	 */
	public function post_content( $content = '' ) {
		global $post;

		if ( 'gistpen' === $post->post_type ) {
			$zip = $this->database->query()->by_post( $post );

			if ( is_wp_error( $zip ) ) {
				// @todo handle each error
				return '';
			}

			$content .= $zip->get_post_content();

			// @todo this can be refactored away somewhere
			// into Collection object?
			// $revisions = $this->database->query( 'commit' )->all_by_parent_id( $post->ID );

			// if ( ! empty( $revisions ) ) {

			// 	foreach ( $revisions as $revision ) {
			// 		$content .= "<small>";
			// 		$content .= hash( 'md5', $revision->get_post_content() );
			// 		$content .= "</small>";
			// 		$content .= "<br>";
			// 	}
			// }

		}

		return $content;
	}

	/**
	 * Filter the child posts from the main query
	 *
	 * @param  \WP_Query $query query object
	 * @return \WP_Query
	 * @since  0.4.0
	 */
	public function pre_get_posts( $query ) {
		if ( ! $query->is_main_query() ) {
			return $query;
		}

		if ( ! $query->is_post_type_archive( 'gistpen' ) ) {
			return $query;
		}

		// only top level posts
		$query->set( 'post_parent', 0 );

		return $query;
	}

	/**
	 * Register the shortcode to embed the Gistpen
	 *
	 * @param    array      $atts    attributes passed into the shortcode
	 * @return   string
	 * @since    0.1.0
	 */
	public function add_shortcode( $atts ) {

		$args = shortcode_atts(
			array(
				'id' => null,
				'highlight' => null,
			), $atts,
			'gistpen'
		);

		// If the user didn't provide an ID, raise an error
		if ( $args['id'] === null ) {
			return '<div class="wp-gistpen-error">No Gistpen ID was provided.</div>';
		}

		$zip = $this->database->query()->by_id( $args['id'] );

		if ( is_wp_error( $zip ) ) {
			// @todo each error
			return '';
		}

		return $zip->get_shortcode_content( $args['highlight'] );

	}

}

