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
	 * Zip description
	 *
	 * @var string
	 * @since 0.4.0
	 */
	protected $description = '';

	/**
	 * Files contained by the Zip
	 *
	 * @var array
	 * @since 0.4.0
	 */
	protected $files;

	/**
	 * Post's ID
	 *
	 * @var int
	 * @since 0.4.0
	 */
	protected $ID = null;

	protected $status = '';

	protected $password = '';

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

		if ( isset( $file->ID ) ) {
			$this->files[$file->ID] = $file;
		} else {
			$this->files[] = $file;
		}
	}

	public function add_files( $files ) {
		foreach ( $files as $file ) {
			$this->add_file( $file );
		}
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

	public function set_status( $status ) {
		// @todo this needs validation
		$this->status = $status;
	}

	public function get_status() {
		return $this->status;
	}

	public function set_password( $password ) {
		// @todo what kind of data does this need to be? hashed, etc.?
		$this->password = $password;
	}

	public function get_password() {
		return $this->password;
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

}
