<?php
namespace WP_Gistpen\Adapter;

use WP_Gistpen\Model\File as FileModel;

/**
 * This is the class description.
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      [current version]
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
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	public function by_array( $data ) {
		$file = new FileModel( $this->plugin_name, $this->version );

		$data = array_intersect_key( $data, array_flip( array( "ID", "slug", "code" ) ) );

		foreach ( $data as $key => $value ) {
			$function = 'set_' . $key;
			$file->{$function}( $value );
		}

		return $file;
	}

	public function by_post( $post ) {
		$file = new FileModel( $this->plugin_name, $this->version );

		if ( isset( $post->ID ) ) {
			$file->set_ID( $post->ID );
		}
		if ( isset( $post->post_content ) ) {
			$file->set_code( $post->post_content );
		}
		if ( isset( $post->post_name ) ) {
			$file->set_slug( $post->post_name );
		}

		return $file;
	}

	public function blank() {
		return new FileModel( $this->plugin_name, $this->version );
	}
}
