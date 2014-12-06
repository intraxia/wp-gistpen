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
		if ( isset( $post->post_title ) ) {
			$file->set_slug( $post->post_title );
		}

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

	/**
	 * Transforms an array of files to json
	 *
	 * @param  array  $files array of files to transform
	 * @return string        file data in json
	 */
	public function to_json($files) {
		if ( empty( $files ) ) {
				return json_encode( array() );
		}

		$json = array();

		foreach ( $files as $file ) {

			$data = new \stdClass;
			$data->slug = $file->get_slug();
			$data->code = $file->get_code();
			$data->ID = $file->get_ID();
			$data->language = new \stdClass;
			$data->language->slug = $file->get_language()->get_slug();

			$json[] = $data;
		}

		return json_encode( $json );
	}
}
