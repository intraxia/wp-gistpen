<?php
namespace WP_Gistpen\Model;

/**
 * Manages the Gistpen's zip data
 *
 * Acts as a container for all the files that an
 * individual Gistpen can hold, as well as metadata
 * about the Gistpen.
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Zip {

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

	/**
	 * Post's status
	 *
	 * @var string
	 * @since 0.5.0
	 */
	protected $status = '';

	/**
	 * Post's password
	 *
	 * @var string
	 * @since 0.5.0
	 */
	protected $password = '';

	/**
	 * Zips's Gist ID
	 *
	 * @var string
	 * @since 0.5.0
	 */
	protected $gist_id = 'none';

	/**
	 * Zips's sync status
	 *
	 * @var   string
	 * @since 0.5.0
	 */
	protected $sync = 'off';

	/**
	 * Date craeted in GMT
	 *
	 * @var string
	 * @since    0.5.0
	 */
	protected $create_date = '';

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->files = array();

	}

	/**
	 * Get the zip's description
	 *
	 * @return string
	 * @since 0.5.0
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Validate and set the zip's description
	 *
	 * @param string $description Zip's description
	 * @since 0.5.0
	 */
	public function set_description( $description ) {
		$this->description = $description;
	}

	/**
	 * Get the zip's files
	 *
	 * @return string
	 * @since 0.5.0
	 */
	public function get_files() {
		return $this->files;
	}

	/**
	 * Validate and add a file to the zip
	 *
	 * @param File $file File model object
	 * @throws Exception If not a File model object
	 * @since 0.5.0
	 */
	public function add_file( $file ) {
		if ( ! $file instanceof File ) {
			throw new Exception( 'File objects only added to files' );
		}

		$file_id = $file->get_ID();

		if ( null !== $file_id ) {
			$this->files[ $file_id ] = $file;
		} else {
			$this->files[] = $file;
		}
	}

	/**
	 * Add an array of files to the zip
	 *
	 * @param array $files Array of Files model objects
	 * @since 0.5.0
	 */
	public function add_files( $files ) {
		foreach ( $files as $file ) {
			$this->add_file( $file );
		}
	}

	/**
	 * Get the zip's DB ID
	 *
	 * @return int File's db ID
	 * @since  0.4.0
	 */
	public function get_ID() {
		return $this->ID;
	}

	/**
	 * Set the zip's DB ID as integer
	 *
	 * @param  int $ID DB id
	 * @since  0.5.0
	 */
	public function set_ID( $ID ) {
		$this->ID = (int) $ID;
	}

	/**
	 * Get the zip's post status
	 * @return string Zip's post_status
	 * @since 0.5.0
	 */
	public function get_status() {
		return $this->status;
	}

	/**
	 * Set the zip's post status
	 *
	 * @param string $status Zip's post_status
	 * @since 0.5.0
	 */
	public function set_status( $status ) {
		// @todo this needs validation
		$this->status = $status;
	}

	/**
	 * Get the zip's password
	 *
	 * @return string Zip's post_password
	 * @since 0.5.0
	 */
	public function get_password() {
		return $this->password;
	}

	/**
	 * Set the zip's password
	 *
	 * @param string $password Zip's post_password
	 * @since 0.5.0
	 */
	public function set_password( $password ) {
		// @todo what kind of data does this need to be? hashed, etc.?
		$this->password = $password;
	}

	/**
	 * Get the zip's Gist ID
	 *
	 * @return string Zip's Gist ID
	 * @since  0.5.0
	 */
	public function get_gist_id() {
		return $this->gist_id;
	}

	/**
	 * Set the zip's Gist ID
	 *
	 * @param string $gist_id Zip's Gist ID
	 * @since  0.5.0
	 */
	public function set_gist_id( $gist_id ) {
		$this->gist_id = $gist_id;
	}

	/**
	 * Get the zip's sync status
	 *
	 * @return bool Zip's sync status
	 * @since  0.5.0
	 */
	public function get_sync() {
		return $this->sync;
	}

	/**
	 * Set the zip's sync status
	 *
	 * @param  bool   $sync Zip's sync status
	 * @since  0.5.0
	 */
	public function set_sync( $sync ) {
		if ( 'on' !== $sync ) {
			$sync = 'off';
		}

		$this->sync = $sync;
	}

	/**
	 * Get the date this Commit was made
	 *
	 * @return string Date created in GMT
	 * @since  0.5.0
	 */
	public function get_create_date() {
		return $this->create_date;
	}

	/**
	 * Validate & set the date this Commit was made
	 *
	 * @return string Date created in GMT
	 * @since  0.5.0
	 */
	public function set_create_date( $create_date ) {
		// @todo validate date
		$this->create_date = $create_date;
	}

	/**
	 * Get's the zip's post content for display
	 * on the front-end
	 *
	 * @return string Zip's post content
	 * @since 0.4.0
	 */
	public function get_post_content() {
		$post_content = '';

		if ( ! empty( $this->files ) ) {
			foreach ( $this->files as $file ) {
				$post_content .= $file->get_post_content();
			}
		}

		return $post_content;
	}

	/**
	 * Get's the zip's shortcode content for display
	 * on the front-end
	 *
	 * @return string Zip's post content
	 * @since 0.4.0
	 */
	public function get_shortcode_content() {
		$shortcode_content = '';

		if ( ! empty( $this->files ) ) {
			foreach ( $this->files as $file ) {
				$shortcode_content .= $file->get_shortcode_content();
			}
		}

		return $shortcode_content;
	}

}
