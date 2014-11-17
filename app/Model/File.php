<?php
namespace WP_Gistpen\Model;

/**
 * @package   WP_Gistpen
 * @author    James DiGioia <jamesorodig@gmail.com>
 * @license   GPL-2.0+
 * @link      http://jamesdigioia.com/wp-gistpen/
 * @copyright 2014 James DiGioia
 */

use \WP_Post;

/**
 * This class holds a Gistpen file's information.
 *
 * @package WP_Gistpen_File
 * @author  James DiGioia <jamesorodig@gmail.com>
 */
class File {

	/**
	 * File's slug
	 *
	 * @var string
	 * @since  0.4.0
	 */
	private $slug;

	/**
	 * File's raw code
	 *
	 * @var string
	 * @since 0.4.0
	 */
	private $code;

	/**
	 * File's ID
	 * @var int
	 * @since 0.4.0
	 */
	protected $ID;

	/**
	 * File's language object
	 *
	 * @var WP_Gistpen\Model\Language
	 * @since 0.4.0
	 */
	private $language;

	/**
	 * Lines to highlight in shortcode
	 *
	 * @var string
	 * @since 0.4.0
	 */
	protected $highlight = null;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Get the file's slug
	 *
	 * @since  0.5.0
	 * @return string File slug
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * Set the file's slug
	 *
	 * @since  0.5.0
	 * @param string $slug File slug
	 */
	public function set_slug( $slug ) {
		$this->slug = $slug;
	}

	/**
	 * Get the file's code
	 *
	 * @since  0.5.0
	 * @return string File code
	 */
	public function get_code() {
		return $this->code;
	}

	/**
	 * Set the file's slug
	 *
	 * @since  0.5.0
	 * @param string $code File code
	 */
	public function set_code( $code ) {
		$this->code = $code;
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

	/**
	 * Get the file's language
	 *
	 * @since  0.5.0
	 * @return string File language
	 */
	public function get_language() {
		return $this->language;
	}

	/**
	 * Set the file's slug
	 *
	 * @since  0.5.0
	 * @param string $language File language
	 */
	public function set_language( $language ) {

		if ( ! $language instanceof Language ) {
			throw new \Exception( __( "Must be Language object", $this->plugin_name ), 1);
		}

		$this->language = $language;
	}

	/**
	 * [description]
	 *
	 * @since  0.4.0
	 * @return [type] [description]
	 */
	public function get_filename() {
		return $this->slug . '.' . $this->language->get_file_ext();
	}

	/**
	 * [description]
	 *
	 * @since  0.4.0
	 * @return [type] [description]
	 */
	public function get_post_content() {
		$post_content = '<div id="wp-gistpenfile-' . $this->slug . '">';

		$post_content .= '<h3 class="wp-gistpenfile-title">' . $this->get_filename() . '</h3>';

		$post_content .= '<pre class="gistpen line-numbers"';

		// Line highlighting and offset will go here
		if( $this->highlight !== null ) {
			$post_content .= 'data-line="' . $this->highlight . '"';
		}

		$post_content .= '>';

		$post_content .= '<code class="language-' . $this->language->get_prism_slug() . '">' . htmlentities( $this->code );
		$post_content .= '</code></pre>';

		$post_content .= '</div>';

		return $post_content;

	}

	/**
	 * [description]
	 *
	 * @since  0.4.0
	 * @return [type] [description]
	 */
	public function get_shortcode_content( $highlight = null ) {
		$this->highlight = $highlight;

		return $this->get_post_content();
	}

	/**
	 * Updates the post object with object details
	 *
	 * @since 0.4.0
	 */
	// public function update_post() {
	// 	$this->file->post_name = strtolower( str_replace( " ", "-", $this->slug ) );
	// 	$this->file->post_content = $this->code;

	// 	$this->language->update_post();
	// }

	// /**
	//  * Update the post object's parent ID
	//  *
	//  * @param  int   $parent_id   ID of parent Gistpen
	//  * @since 0.4.0
	//  */
	// public function set_parent_id( $parent_id ) {
	// 	$this->parent_id = $parent_id;
	// }

	// /**
	//  * Update the post object's post status
	//  *
	//  * @param  int   $parent_id   ID of parent Gistpen
	//  * @since 0.4.0
	//  */
	// public function set_post_status( $post_status ) {
	// 	$this->file->post_status = $post_status;
	// }

	// /**
	//  * Update the post object's time
	//  *
	//  * @param  int   $parent_id   ID of parent Gistpen
	//  * @since 0.4.0
	//  */
	// public function set_post_date( $post_date ) {
	// 	$this->post_date = $post_date;
	// }

	// /**
	//  * Update the post object's time
	//  *
	//  * @param  int   $parent_id   ID of parent Gistpen
	//  * @since 0.4.0
	//  */
	// public function set_post_date_gmt( $post_date_gmt ) {
	// 	$this->post_date_gmt = $post_date_gmt;
	// }

}
