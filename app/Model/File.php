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
	protected $slug = '';

	/**
	 * File's raw code
	 *
	 * @var string
	 * @since 0.4.0
	 */
	protected $code = '';

	/**
	 * File's ID
	 * @var int
	 * @since 0.4.0
	 */
	protected $ID = null;

	/**
	 * File's language object
	 *
	 * @var WP_Gistpen\Model\Language
	 * @since 0.4.0
	 */
	protected $language;

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
			throw new \Exception( __( 'set_language requires a Model\Language object', $this->plugin_name ), 1);
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

}
