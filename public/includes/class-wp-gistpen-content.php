<?php
/**
 * @package   WP_Gistpen
 * @author    James DiGioia <jamesorodig@gmail.com>
 * @license   GPL-2.0+
 * @link      http://jamesdigioia.com/wp-gistpen/
 * @copyright 2014 James DiGioia
 */

/**
 * This class manipulates the Gistpen post content.
 *
 * @package WP_Gistpen_Content
 * @author  James DiGioia <jamesorodig@gmail.com>
 */
class WP_Gistpen_Content {

	/**
	 * Post object of the Gistpen we're
	 * currently manipupulating
	 *
	 * @var $post object
	 * @since  0.3.0
	 */
	public static $gistpen;

	/**
	 * Content of the Gistpen we're
	 * currently manipupulating
	 *
	 * @var string post_content
	 * @since  0.3.0
	 */
	public static $content;

	/**
	 * Array of all the files on this Gistpen
	 *
	 * @var array
	 * @since  0.4.0
	 */
	public static $files = array();

	/**
	 * Line numbers to highlight
	 *
	 * @var string
	 * @since 0.3.0
	 */
	public static $highlight = null;

	/**
	 * Remove extra filters from the Gistpen content
	 *
	 * @since    0.1.0
	 */
	public static function remove_filters( $content ) {

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
	public static function post_content( $content ) {

		if( 'gistpen' == get_post_type() ) {
			return self::get_post_content( get_post() );
		}

		return $content;
	}

	/**
	 * Register the shortcode to embed the Gistpen
	 *
	 * @param    array      $atts    attributes passed into the shortcode
	 * @return   string
	 * @since    0.1.0
	 */
	public static function add_shortcode( $atts ) {

		$args = shortcode_atts( array(
			'id' => null,
			'highlight' => null),
			$atts,
			'gistpen'
		);

		return self::get_shortcode_content( $args );

	}

	/**
	 * Returns the Gistpen content used
	 * in the normal loop
	 *
	 * @param WP_Post $gistpen Gistpen post object
	 * @return string     manipulated content
	 * @since  0.3.0
	 */
	public static function get_post_content( $gistpen ) {

		// Content needs to be cleared if we're on the archive page
		self::$content = '';

		self::$gistpen = $gistpen;
		self::$files = get_children( array( 'post_parent' => self::$gistpen->ID ) );

		if( !empty(self::$files) ) {
			foreach( self::$files as $file ) {
				self::$gistpen = $file;
				self::add_code_markup();
			}
		}

		return self::$content;
	}

	/**
	 * Returns the Gistpen content for the
	 * shortcode
	 *
	 * @return string     manipulated content
	 * @since  0.3.0
	 */
	public static function get_shortcode_content( $args ) {

		// Content needs to be cleared if we're calling multiple shortcodes in one post
		self::$content = '';

		// If the user didn't provide an ID, raise an error
		if( $args['id'] === null ) {
			return '<div class="wp-gistpen-error">No Gistpen ID was provided.</div>';
		}

		$gistpen = get_post( $args['id'] );

		if ( $gistpen->post_type !== 'gistpen' ) {
			return '<div class="wp-gistpen-error">The ID supplied is not a Gistpen.</div>';
		}

		self::$gistpen = $gistpen;
		self::$highlight = $args['highlight'];

		self::$files = get_children( array( 'post_parent' => self::$gistpen->ID ) );

		if( !empty( self::$files ) ) {
			foreach( self::$files as $file ) {
				self::$gistpen = $file;
				self::add_code_markup();
			}
		} else {
			self::add_code_markup();
		}

		return self::$content;

	}

	/**
	 * Wrap content in code tags
	 * and add gistpen & language classes
	 *
	 * @return   string               the tagged and classed content
	 * @since    0.1.0
	 */
	private static function add_code_markup() {
		$content = '';

		$content .= '<h2 class="wp-gistpenfile-title">' . self::$gistpen->post_name . '.' . self::get_the_language_extension( self::$gistpen->ID ) . '</h2>';

		$content .= '<pre id="wp-gistpenfile-' . self::$gistpen->post_name . '" class="gistpen line-numbers" ';

		if( self::$highlight !== null ) {
			$content .= 'data-line="' . self::$highlight . '"';
		}

		$content .= '>';


		$content .= '<code class="language-' . self::get_the_language( self::$gistpen->ID ) . '">' . self::$gistpen->post_content;
		$content .= '</code></pre>';


		self::$content .= $content;
	}

	/**
	 * Get the language slug from an ID
	 *
	 * @param  int    $gistpen_id    ID of the Gistpen
	 * @return string             language slug
	 * @since    0.4.0
	 */
	public static function get_the_language( $gistpen_id ) {
		$terms = get_the_terms( $gistpen_id, 'language' );

		if( $terms ) {
			$lang = array_pop( $terms );
			$slug = ($lang->slug == 'js' ? 'javascript' : ($lang->slug == 'sass' ? 'scss' : $lang->slug));
		} else {
			$slug = 'none';
		}

		return $slug;
	}

	/**
	 * Get the file extension for the language
	 *
	 * @param  in     $gistpen_id ID of the Gistpen
	 * @return string             language extension
	 * @since    0.4.0
	 */
	public static function get_the_language_extension( $gistpen_id ) {
		$slug = self::get_the_language ( $gistpen_id );
		$slug = ( $slug == 'javascript' ? 'js' : ( $slug == 'bash' ? 'sh' : ( $slug == 'scss' ? 'sass' : $slug ) ) );

		return $slug;
	}

}

