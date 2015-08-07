<?php
namespace Intraxia\Gistpen\Adapter;

use Intraxia\Gistpen\Model\Commit\State as StateModel;

/**
 * Builds State models based on various data inputs
 *
 * @package    Intraxia\Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class State {

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
	 * @param  \WP_Post $post zip's post data
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
	 * @return StateModel   file model
	 * @since 0.5.0
	 */
	public function blank() {
		return new StateModel();
	}
}
