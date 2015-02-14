<?php
namespace WP_Gistpen\Adapter;

use WP_Gistpen\Model\Commit\State as StateModel;

/**
 * Builds State models based on various data inputs
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class State {

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
	 * Build a State model by array of data
	 *
	 * @param  array $data array of data
	 * @return State        State model
	 * @since  0.5.0
	 */
	public function by_array( $data ) {
		$state = $this->blank();

		$data = array_intersect_key( $data, array_flip( array( 'ID', 'slug', 'code', 'gist_id', 'status' ) ) );

		foreach ( $data as $key => $value ) {
			$function = 'set_' . $key;
			$state->{$function}( $value );
		}

		return $state;
	}

	/**
	 * Build a State model by $post data
	 *
	 * @param  WP_Post $post zip's post data
	 * @return State         State model
	 * @since 0.5.0
	 */
	public function by_post( $post ) {
		$state = $this->blank();

		if ( isset( $post->ID ) ) {
			$state->set_ID( $post->ID );
		}
		if ( isset( $post->post_parent ) ) {
			$state->set_head_id( $post->post_parent );
		}
		if ( isset( $post->post_content ) ) {
			$state->set_code( $post->post_content );
		}
		if ( isset( $post->post_title ) && $post->post_title !== '' ) {
			$state->set_slug( $post->post_title );
		} else {
			$state->set_slug( $post->post_name );
		}
		if ( isset( $post->status ) ) {
			$state->set_status( $post->status );
		}
		if ( isset( $post->gist_id ) ) {
			$state->set_gist_id( $post->gist_id );
		}

		return $state;
	}

	/**
	 * Builds a blank file model
	 *
	 * @return File   file model
	 * @since 0.5.0
	 */
	public function blank() {
		return new StateModel( $this->plugin_name, $this->version );
	}
}
