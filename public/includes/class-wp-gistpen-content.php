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
	private static $gistpen;

	/**
	 * Content of the Gistpen we're
	 * currently manipupulating
	 *
	 * @var string post_content
	 * @since  0.3.0
	 */
	private static $content;

	/**
	 * Array of all the files on this Gistpen
	 *
	 * @var array
	 * @since  0.4.0
	 */
	private static $files = array();

	/**
	 * Line numbers to highlight
	 *
	 * @var string
	 * @since 0.3.0
	 */
	private static $highlight = null;

	/**
	 * Returns the Gistpen content used
	 * in the normal loop
	 *
	 * @return string     manipulated content
	 * @since  0.3.0
	 */
	public static function get_post_content( $gistpen ) {

		self::$gistpen = $gistpen;
		self::$files = get_posts( array(
			'post_type' => 'gistpens',
			'numberposts' => -1,
			'post_parent' => self::$gistpen->ID,
			//'post_status' => 'any'
		));

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

		// If the user didn't provide an ID, raise an error
		if( $args['id'] == null ) {
			return '<div class="gistpen-error">No Gistpen ID was provided.</div>';
		}

		$gistpen = get_post( $args['id'] );

		if ( $gistpen->post_type !== 'gistpens' ) {
			return '<div class="wp-gistpen-error">The ID supplied is not a Gistpen.</div>';
		}

		self::$gistpen = $gistpen;
		self::$highlight = $args['highlight'];

		self::add_code_markup();

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

		$content .= '<h2 class="wp-gistpenfile-title">' . self::$gistpen->post_name . '</h2>';

		$content .= '<pre id="wp-gistpenfile-' . self::$gistpen->post_name . '" class="gistpen line-numbers" ';

		if( self::$highlight !== null ) {
			$content .= 'data-line="' . self::$highlight . '"';
		}

		$content .= '>';

		$slug = self::get_the_language( self::$gistpen->ID );

		$content .= '<code class="language-' . $slug . '">' . self::$gistpen->post_content;
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
			$slug = ($lang->slug == 'js' ? 'javascript' : $lang->slug);
			$slug = ($lang->slug == 'sass' ? 'scss' : $slug);
		} else {
			$slug = 'none';
		}

		return $slug;
	}

}

