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
		self::$content = $gistpen->post_content;

		self::add_code_markup();
		self::add_description();

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
			return '<div class="gistpen-error">The ID supplied is not a Gistpen.</div>';
		}

		self::$gistpen = $gistpen;
		self::$content = $gistpen->post_content;
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

		$terms = get_the_terms( self::$gistpen->ID, 'language' );

		$content = '<pre id="gistpen-' . self::$gistpen->post_name . '" class="gistpen line-numbers" ';

		if( self::$highlight !== null ) {
			$content .= 'data-line="' . self::$highlight . '"';
		}

		$content .= '>';

		if( $terms ) {
			$lang = array_pop( $terms );
			$slug = ($lang->slug == 'js' ? 'javascript' : $lang->slug);
			$slug = ($lang->slug == 'sass' ? 'scss' : $slug);
		} else {
			$slug = 'none';
		}

		$content .= '<code class="language-' . $slug . '">' . self::$content;
		$content .= '</code></pre>';

		self::$content = $content;

	}

	/**
	 * Add Gistpen description to content
	 *
	 * @param    string   $content   post_content
	 * @return   string              the content with description
	 * @since    0.1.0
	 */
	private static function add_description() {

		// Grab the description text
		$description_text = get_post_meta( self::$gistpen->ID, '_wpgp_gistpen_description', true );

		// Wrap it
		$description_html = '<div class="gistpen-description">';
		$description_html .= $description_text;
		$description_html .= '</div>';

		// Add it to the content
		self::$content .= $description_html;

	}

}

