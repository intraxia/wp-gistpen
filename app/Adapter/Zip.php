<?php
namespace WP_Gistpen\Adapter;

use WP_Gistpen\Model\Zip as ZipModel;

/**
 * This is the class description.
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      [current version]
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
		$zip = new ZipModel( $this->plugin_name, $this->version );

		$data = array_intersect_key( $data, array_flip( array( "description", "ID", "status", "password" ) ) );

		foreach ( $data as $key => $value ) {
			$function = 'set_' . $key;
			$zip->{$function}( $value );
		}

		return $zip;
	}

	public function by_post( $post ) {
		$zip = new ZipModel( $this->plugin_name, $this->version );

		if ( isset( $post->ID ) ) {
			$zip->set_ID( $post->ID );
		}
		if ( isset( $post->post_title ) ) {
			$zip->set_description( $post->post_title );
		}
		if ( isset( $post->post_status ) ) {
			$zip->set_status( $post->post_status );
		}
		if ( isset( $post->post_password ) ) {
			$zip->set_password( $post->post_password );
		}

		return $zip;
	}

	public function blank() {
		return new ZipModel( $this->plugin_name, $this->version );
	}

}
