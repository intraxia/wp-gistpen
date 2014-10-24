<?php
namespace WP_Gistpen;

/**
 * @package   WP_Gistpen
 * @author    James DiGioia <jamesorodig@gmail.com>
 * @license   GPL-2.0+
 * @link      http://jamesdigioia.com/wp-gistpen/
 * @copyright 2014 James DiGioia
 */

use WP_Gistpen\Database\Query;

/**
 * This class manipulates the Gistpen post content.
 *
 * @package Content
 * @author  James DiGioia <jamesorodig@gmail.com>
 */
class Content {

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

	}

	/**
	 * Remove extra filters from the Gistpen content
	 *
	 * @since    0.1.0
	 */
	public function remove_filters( $content ) {

		if( 'gistpen' == get_post_type() ) {
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
	 * @param string $atts shortcode attributes
	 * @return string post_content
	 * @since    0.1.0
	 */
	public function post_content( $content = '' ) {
		global $post;

		if( 'gistpen' == $post->post_type ) {
			$zip = Query::get( $post );

			if( is_wp_error( $zip ) ) {
				// @todo handle each error
				return;
			}

			return $zip->post_content;
		}

		return $content;
	}

	/**
	 * Filter the child posts from the main query
	 *
	 * @param  WP_Query $query query object
	 * @since  0.4.0
	 */
	public function pre_get_posts( $query ) {
		if ( ! $query->is_main_query() ) {
			return;
		}

		if ( ! $query->is_post_type_archive( 'gistpen' ) ) {
			return;
		}

		// only top level posts
		$query->set( 'post_parent', 0 );

		return $query;
	}

}

