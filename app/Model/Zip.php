<?php
namespace WP_Gistpen\Model;
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
class Zip {

	/**
	 * Gistpen's description
	 *
	 * @var string
	 * @since 0.4.0
	 */
	public $description = '';

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

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->files = array();

	}

	public function get_description() {
		return $this->description;
	}

	public function set_description( $description ) {
		$this->description = $description;
	}

	public function get_files() {
		return $this->files;
	}

	public function add_file( $file ) {
		if ( ! $file instanceof File ) {
			throw new Exception("File objects only added to files");
		}

		$this->files[] = $file;
	}

	/**
	 * Get the file's DB ID
	 *
	 * @since  0.4.0
	 * @return int File's db ID
	 */
	public function get_ID() {
		return $this->ID;
	}

	/**
	 * Set the file's DB ID as integer
	 *
	 * @since  0.5.0
	 * @param  int $ID DB id
	 */
	public function set_ID( $ID ) {
		$this->ID = (int) $ID;
	}

	public function get_post_content() {
		$post_content = '';

		if( ! empty( $this->files ) ) {
			foreach ( $this->files as $file ) {
				$post_content .= $file->get_post_content();
			}
		}

		return $post_content;
	}

	public function get_shortcode_content() {
		$shortcode_content = '';

		if( ! empty( $this->files ) ) {
			foreach ( $this->files as $file ) {
				$shortcode_content .= $file->get_shortcode_content();
			}
		}

		return $shortcode_content;
	}

	// /**
	//  * Updates the post object with object details
	//  *
	//  * @since 0.4.0
	//  */
	// public function update_post() {
	// 	if ( isset( $this->description ) ) {
	// 		$this->post->post_title = $this->description;
	// 	}

	// 	foreach ( $this->files as &$file ) {
	// 		$file->update_post();
	// 		$file->update_status( $this->post->post_status );
	// 	}

	// 	unset( $file );
	// }

}
