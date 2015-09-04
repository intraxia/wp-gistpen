<?php
namespace Intraxia\Gistpen\Adapter;

use Intraxia\Gistpen\Model\Commit\Meta as CommitModel;

/**
 * Builds commit models based on various data inputs
 *
 * @package    Intraxia\Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Commit {

	/**
	 * Build a Commit model by json string
	 *
	 * @param  string $data json of commit data
	 * @return File         File model
	 * @since 0.5.0
	 */
	public function by_array( $data ) {
		$commit = $this->blank();

		$data = array_intersect_key( $data, array_flip( array( 'ID', 'description', 'status', 'gist_id', 'create_date' ) ) );

		foreach ( $data as $key => $value ) {
			$function = 'set_' . $key;
			$commit->{$function}( $value );
		}

		return $commit;
	}

	/**
	 * Build a Commit model by $post data
	 *
	 * @param  \WP_Post $post Commit's post data
	 * @return Commit       Commit model
	 * @since 0.5.0
	 */
	public function by_post( $post ) {
		$commit = $this->blank();

		if ( isset( $post->ID ) ) {
			$commit->set_ID( $post->ID );
		}
		if ( isset( $post->post_title ) ) {
			$commit->set_description( $post->post_title );
		}
		if ( isset( $post->post_status ) ) {
			$commit->set_status( $post->post_status );
		}
		if ( isset( $post->post_parent ) ) {
			$commit->set_head_id( $post->post_parent );
		}
		if ( isset( $post->gist_id ) ) {
			$commit->set_gist_id( $post->gist_id );
		}
		if ( isset( $post->post_date_gmt ) ) {
			$commit->set_create_date( $post->post_date_gmt );
		}

		return $commit;
	}

	/**
	 * Create a blank commit model
	 *
	 * @return \Gistpen\Model\Commit\Meta
	 * @since 0.5.0
	 */
	public function blank() {
		return new CommitModel();
	}
}
