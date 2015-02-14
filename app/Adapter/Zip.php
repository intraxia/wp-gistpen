<?php
namespace WP_Gistpen\Adapter;

use WP_Gistpen\Model\Zip as ZipModel;

/**
 * Builds zip models based on various data inputs
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

	/**
	 * Build a Zip model by array of data
	 *
	 * @param  array $data array of data
	 * @return Zip       Zip model
	 * @since 0.5.0
	 */
	public function by_array( $data ) {
		$zip = $this->blank();

		$data = array_intersect_key( $data, array_flip( array( 'description', 'ID', 'status', 'password', 'gist_id', 'sync', 'create_date' ) ) );

		foreach ( $data as $key => $value ) {
			$function = 'set_' . $key;
			$zip->{$function}( $value );
		}

		return $zip;
	}

	/**
	 * Build a Zip model by $post data
	 *
	 * @param  WP_Post $post zip's post data
	 * @return Zip       Zip model
	 * @since 0.5.0
	 */
	public function by_post( $post ) {
		$zip = $this->blank();

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
		if ( isset( $post->gist_id ) ) {
			$zip->set_gist_id( $post->gist_id );
		}
		if ( isset( $post->sync ) ) {
			$zip->set_sync( $post->sync );
		}
		if ( isset( $post->post_date_gmt ) ) {
			$zip->set_create_date( $post->post_date_gmt );
		}

		return $zip;
	}

	/**
	 * Build a Zip model by Gist API data
	 *
	 * @param  array $gist Gist API data
	 * @return Zip
	 * @since  0.5.0
	 */
	public function by_gist( $gist ) {
		$zip = $this->blank();

		$zip->set_description( $gist['description'] );

		if ( $gist['public'] ) {
			$status = 'publish';
		} else {
			$status = 'private';
		}
		$zip->set_status( $status );

		$zip->set_gist_id( $gist['id'] );

		$time = str_replace( 'Z', '', str_replace( 'T', ' ', $gist['created_at'] ) );
		$zip->set_create_date( $time );

		return $zip;
	}


	/**
	 * Builds a blank zip model
	 *
	 * @return Zip zip model
	 * @since 0.5.0
	 */
	public function blank() {
		return new ZipModel( $this->plugin_name, $this->version );
	}

}
