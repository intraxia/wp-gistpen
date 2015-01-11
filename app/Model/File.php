<?php
namespace WP_Gistpen\Model;

/**
 * Manages the Gistpen's file data
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class File {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.5.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.5.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

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
		$this->slug = strtolower( str_replace( ' ', '-', $slug ) );
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
		if ( ! isset( $this->language) ) {
			return null;
		}

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
			throw new \Exception( __( 'set_language requires a Model\Language object', $this->plugin_name ), 1 );
		}

		$this->language = $language;
	}

	/**
	 * Get the file's filename with file extension
	 *
	 * @since  0.4.0
	 * @return string filename w/ ext
	 */
	public function get_filename() {
		// Do we want to readd file ext as an option?
		// Note that we currently can't export Gists with language data.
		// The file extension is the only we get that data in properly.
		return $this->slug; // . '.' . $this->language->get_file_ext();
	}

	/**
	 * Get's the file's post content for display
	 * on the front-end
	 *
	 * @return string File's post content
	 * @since 0.4.0
	 */
	public function get_post_content() {
		$post_content = '<div id="wp-gistpenfile-' . $this->slug . '">';

		$post_content .= '<h3 class="wp-gistpenfile-title">' . $this->get_filename() . '</h3>';

		$post_content .= '<pre class="gistpen line-numbers"';

		// Line highlighting and offset will go here
		if ( $this->highlight !== null ) {
			$post_content .= 'data-line="' . $this->highlight . '"';
		}

		$post_content .= '>';

		$post_content .= '<code class="language-' . $this->language->get_prism_slug() . '">' . htmlentities( $this->code );
		$post_content .= '</code></pre>';

		$post_content .= '</div>';

		return $post_content;

	}

	/**
	 * Get's the file's shortcode content for display
	 * on the front-end
	 *
	 * @return string File's post content
	 * @since 0.4.0
	 */
	public function get_shortcode_content( $highlight = null ) {
		$this->highlight = $highlight;

		return $this->get_post_content();
	}

}
