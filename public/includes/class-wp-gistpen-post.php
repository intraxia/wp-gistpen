<?php
/**
 * @package   WP_Gistpen
 * @author    James DiGioia <jamesorodig@gmail.com>
 * @license   GPL-2.0+
 * @link      http://jamesdigioia.com/wp-gistpen/
 * @copyright 2014 James DiGioia
 */

/**
 * This class contains the Gistpen data.
 *
 * @package WP_Gistpen_Post
 * @author  James DiGioia <jamesorodig@gmail.com>
 */
class WP_Gistpen_Post extends WP_Gistpen_Abtract {

	/**
	 * Gistpen's post_object
	 *
	 * @var WP_Post
	 * @since 0.4.0
	 */
	protected $post;

	/**
	 * Gistpen's description
	 *
	 * @var string
	 * @since 0.4.0
	 */
	protected $description;

	/**
	 * Files contained in the Gistpen
	 *
	 * @var array
	 * @since 0.4.0
	 */
	protected $files;

	/**
	 * Gistpen's content manipulated for post display
	 *
	 * @var string
	 * @since 0.4.0
	 */
	protected $post_content;

	/**
	 * Gistpen's content manipulated for shortcode display
	 *
	 * @var string
	 * @since 0.4.0
	 */
	protected $shortcode_content;

	public function __construct( WP_Post $post, $files = array() ) {
		$this->post = $post;

		if ( ! is_array( $files ) ) {
			throw new Exception( "Files must be in an array" );
		}

		$this->files = $files;
	}

	protected function get_description() {
		if ( ! isset( $this->description ) ) {
			$this->description = $this->post->post_title;
		}

		return $this->description;
	}
	protected function get_files() {
		return $this->files;
	}
	protected function get_post_content() {
		if( ! isset( $this->post_content ) && ! empty( $this->files ) ) {
			foreach ( $this->files as $file ) {
				$this->post_content .= $file->post_content;
			}
		}

		return $this->post_content;
	}
	protected function get_shortcode_content() {
		if( ! isset( $this->shortcode_content ) && ! empty( $this->files ) ) {
			foreach ( $this->files as $file ) {
				$this->shortcode_content .= $file->shortcode_content;
			}
		}

		return $this->shortcode_content;
	}

}
