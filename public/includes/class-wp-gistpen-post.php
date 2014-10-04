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
	 * Gistpen's WP_Post obj
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
	public $description;

	/**
	 * Files contained in the Gistpen
	 *
	 * @var array
	 * @since 0.4.0
	 */
	public $files;

	/**
	 * Post's ID
	 * @var int
	 * @since 0.4.0
	 */
	public $ID;

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
		$this->ID = $this->post->ID;

		if ( ! is_array( $files ) ) {
			throw new Exception( "Files must be in an array" );
		}

		$this->files = $files;

		$this->description = $this->post->post_title;
	}

	/**
	 * Functions to get protected properties
	 *
	 * @since  0.4.0
	 */
	protected function get_ID() {
		return $this->ID;
	}
	protected function get_post() {
		return $this->post;
	}
	protected function get_files() {
		return $this->files;
	}
	protected function get_post_content() {
		if( ! empty( $this->files ) ) {
			foreach ( $this->files as $file ) {
				$this->post_content .= $file->post_content;
			}
		}

		return $this->post_content;
	}
	public function get_shortcode_content() {
		if( ! isset( $this->shortcode_content ) && ! empty( $this->files ) ) {
			foreach ( $this->files as $file ) {
				$this->shortcode_content .= $file->shortcode_content;
			}
		}

		return $this->shortcode_content;
	}

	/**
	 * Updates the post object with object details
	 *
	 * @since 0.4.0
	 */
	public function update_post() {
		if ( isset( $this->description ) ) {
			$this->post->post_title = $this->description;
		}

		foreach ( $this->files as &$file ) {
			$file->update_post();
			$file->update_status( $this->post->post_status );
		}

		unset( $file );
	}

}
