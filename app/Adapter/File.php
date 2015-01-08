<?php
namespace WP_Gistpen\Adapter;

use WP_Gistpen\Model\File as FileModel;

/**
 * Builds file models based on various data inputs
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
	 * Initialize the class and set its properties.
	 *
	 * @since    0.5.0
	 * @var      string    $plugin_name       The name of this plugin.
	 * @var      string    $version           The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Build a File model by array of data
	 *
	 * @param  array $data array of data
	 * @return File       File model
	 * @since 0.5.0
	 */
	public function by_array( $data ) {
		$file = $this->blank();

		$data = array_intersect_key( $data, array_flip( array( 'ID', 'slug', 'code' ) ) );

		foreach ( $data as $key => $value ) {
			$function = 'set_' . $key;
			$file->{$function}( $value );
		}

		return $file;
	}

	/**
	 * Build a File model by $post data
	 *
	 * @param  WP_Post $post zip's post data
	 * @return File       File model
	 * @since 0.5.0
	 */
	public function by_post( $post ) {
		$file = $this->blank();

		if ( isset( $post->ID ) ) {
			$file->set_ID( $post->ID );
		}
		if ( isset( $post->post_content ) ) {
			$file->set_code( $post->post_content );
		}
		if ( isset( $post->post_title ) && $post->post_title !== '' ) {
			$file->set_slug( $post->post_title );
		} else {
			$file->set_slug( $post->post_name );
		}

		return $file;
	}

	/**
	 * Build a File model by Gist API data
	 *
	 * @param  array     $gist Gist API data
	 * @return FileModel       built File
	 */
	public function by_gist( $gist ) {
		$file = $this->blank();

		$file->set_code( $gist['content'] );
		$file->set_slug( $gist['filename'] );

		return $file;
	}

	/**
	 * Builds a blank file model
	 *
	 * @return File   file model
	 * @since 0.5.0
	 */
	public function blank() {
		return new FileModel( $this->plugin_name, $this->version );
	}
}
