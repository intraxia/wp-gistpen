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
	public $gistpen;

	/**
	 * Content of the Gistpen we're
	 * currently manipupulating
	 *
	 * @var string post_content
	 * @since  0.3.0
	 */
	public $content;

	public function __construct( $gistpen ) {

		$this->gistpen = $gistpen;
		$this->content = $gistpen->post_content;

	}

	/**
	 * Returns the Gistpen content used
	 * in the normal loop
	 *
	 * @return string     manipulated content
	 * @since  0.3.0
	 */
	public function get_post_content() {

		$this->add_code_markup();
		$this->add_description();

		return $this->content;
	}

	/**
	 * Returns the Gistpen content for the
	 * shortcode
	 *
	 * @return string     manipulated content
	 * @since  0.3.0
	 */
	public function get_shortcode_content() {

		$this->add_code_markup();

		return $this->content;

	}

	/**
	 * Wrap content in code tags
	 * and add gistpen & language classes
	 *
	 * @return   string               the tagged and classed content
	 * @since    0.1.0
	 */
	private function add_code_markup() {

		$terms = get_the_terms( $this->gistpen->ID, 'language' );

		$content = '<pre class="gistpen line-numbers">';

		if( $terms ) {
			$lang = array_pop( $terms );
			$slug = ($lang->slug == 'js' ? 'javascript' : $lang->slug);
			$slug = ($lang->slug == 'sass' ? 'scss' : $slug);
		} else {
			$slug = 'none';
		}

		$content .= '<code class="language-' . $slug . '">' . $this->content;
		$content .= '</code></pre>';

		$this->content = $content;

	}

	/**
	 * Add Gistpen description to content
	 *
	 * @param  string   $content   post_content
	 * @return string              the content with description
	 * @since    0.1.0
	 */
	public function add_description() {

		// Grab the description text
		$description_text = get_post_meta( $this->gistpen->ID, '_wpgp_gistpen_description', true );

		// Wrap it
		$description_html = '<div class="gistpen-description">';
		$description_html .= $description_text;
		$description_html .= '</div>';

		// Add it to the content
		$this->content .= $description_html;

	}
}

