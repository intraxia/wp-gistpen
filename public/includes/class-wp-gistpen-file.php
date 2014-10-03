<?php
/**
 * @package   WP_Gistpen
 * @author    James DiGioia <jamesorodig@gmail.com>
 * @license   GPL-2.0+
 * @link      http://jamesdigioia.com/wp-gistpen/
 * @copyright 2014 James DiGioia
 */

/**
 * This class holds a Gistpen file's information.
 *
 * @package WP_Gistpen_File
 * @author  James DiGioia <jamesorodig@gmail.com>
 */
class WP_Gistpen_File extends WP_Gistpen_Abtract {

	/**
	 * File's post_object
	 *
	 * @var WP_Post
	 * @since 0.4.0
	 */
	protected $file;

	/**
	 * File's slug
	 *
	 * @var string
	 * @since  0.4.0
	 */
	public $slug;

	/**
	 * File's filename with extension
	 *
	 * @var string
	 * @since 0.4.0
	 */
	protected $filename;

	/**
	 * File's language object
	 *
	 * @var WP_Gistpen_Language
	 * @since 0.4.0
	 */
	public $language;

	/**
	 * File's raw code
	 *
	 * @var string
	 * @since 0.4.0
	 */
	public $code;

	/**
	 * File's ID
	 * @var int
	 * @since 0.4.0
	 */
	public $ID;

	/**
	 * File's content manipulated for post display
	 *
	 * @var string
	 * @since 0.4.0
	 */
	protected $post_content;

	/**
	 * Lines to highlight in shortcode
	 *
	 * @var string
	 * @since 0.4.0
	 */
	protected $highlight = null;

	/**
	 * File's content manipulated for shortcode display
	 *
	 * @var string
	 * @since 0.4.0
	 */
	protected $shortcode_content;

	public function __construct( WP_Post $file, WP_Gistpen_Language $language ) {
		$this->file = $file;
		$this->language = $language;

		$this->slug = $this->file->post_name;
		$this->code = $this->file->post_content;
		$this->ID = $this->file->ID;
	}

	/**
	 * Functions to get protected properties
	 *
	 * @since  0.4.0
	 */
	protected function get_ID() {
		return $this->ID;
	}
	protected function get_file() {
		return $this->file;
	}
	protected function get_parent() {

		if ( ! isset( $this->parent ) ) {
			$this->parent = get_post( wp_get_post_parent_id( $this->file->ID ) );
		}

		return $this->parent;
	}
	protected function get_filename() {

		if ( ! isset( $this->filename ) ) {
			$this->filename = $this->slug . '.' . $this->language->file_ext;
		}

		return $this->filename;
	}
	protected function get_post_content() {

		if ( ! isset( $this->post_content ) ) {
			$this->post_content .= '<div id="wp-gistpenfile-' . $this->file->post_name . '">';

			$this->post_content .= '<h3 class="wp-gistpenfile-title">' . $this->get_filename() . '</h3>';

			$this->post_content .= '<pre class="gistpen line-numbers"';
			// Line highlighting and offset will go here
			if( $this->highlight !== null ) {
				$this->post_content .= 'data-line="' . $this->highlight . '"';
			}

			$this->post_content .= '>';
			$this->post_content .= '<code class="language-' . $this->language->prism_slug . '">' . htmlentities( $this->code );
			$this->post_content .= '</code></pre>';

			$this->post_content .= '</div>';
		}

		return $this->post_content;
	}
	public function get_shortcode_content( $highlight = null ) {
		$this->highlight = $highlight;

		if ( ! isset( $this->shortcode_content ) ) {
			// @todo This is a stub for future functionality
			$this->shortcode_content = $this->get_post_content();
		}

		return $this->shortcode_content;
	}

	/**
	 * Updates the post object with object details
	 *
	 * @since 0.4.0
	 */
	public function update_post() {
		$this->file->post_name = strtolower( str_replace( " ", "-", $this->slug ) );
		$this->file->post_content = $this->code;

		$this->language->update_post();
	}

	/**
	 * Update the post object's parent ID
	 *
	 * @param  int   $parent_id   ID of parent Gistpen
	 * @since 0.4.0
	 */
	public function update_parent( $parent_id ) {
		$this->file->post_parent = $parent_id;
	}

	/**
	 * Update the post object's post status
	 *
	 * @param  int   $parent_id   ID of parent Gistpen
	 * @since 0.4.0
	 */
	public function update_status( $post_status ) {
		$this->file->post_status = $post_status;
	}

	/**
	 * Update the post object's time
	 *
	 * @param  int   $parent_id   ID of parent Gistpen
	 * @since 0.4.0
	 */
	public function update_timestamps( $post_date, $post_date_gmt ) {
		$this->file->post_date = $post_date;
		$this->file->post_date_gmt = $post_date_gmt;
	}

}
