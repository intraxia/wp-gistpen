<?php
namespace Intraxia\Gistpen\Test\Unit;

use Intraxia\Gistpen\App;
use Intraxia\Gistpen\Test\TestCase as BaseTestCase;
use Intraxia\Jaxion\Core\UndefinedAliasException;
use Mockery;
use WP_UnitTestCase;

abstract class TestCase extends BaseTestCase {
	/**
	 * @var \WP_Post
	 */
	protected $repo;

	/**
	 * @var \WP_Post
	 */
	protected $commit;

	/**
	 * @var int[]
	 */
	protected $blobs;

	/**
	 * @var \WP_Term
	 */
	protected $language;

	/**
	 * @var int
	 */
	protected $user_id;

	/**
	 * @var int[]
	 */
	protected $states;

	public function mock( $alias ) {
		try {
			$to_mock = $this->app->get( $alias );

			return Mockery::mock( get_class( $to_mock ) );
		} catch ( UndefinedAliasException $e ) {
			return Mockery::mock( $alias );
		}
	}

	public function create_post_and_children( $with_revision = true ) {
		$this->repo = $this->factory->gistpen->create_and_get();
		$repo       = (array) $this->repo;
		unset( $repo['ID'] );

		if ( $with_revision ) {
			$this->commit = get_post( wp_insert_post( array_merge( $repo, array(
				'post_parent' => $this->repo->ID,
				'post_type'   => 'revision',
			) ) ) );
		}

		$this->blobs = $this->factory->gistpen->create_many( 3, array(
			'post_parent' => $this->repo->ID,
		) );

		if ( $with_revision ) {
			$this->states = array();
		}

		foreach ( $this->blobs as $blob_id ) {
			wp_set_object_terms( $blob_id, 'php', 'wpgp_language', false );

			$blob = get_post( $blob_id, ARRAY_A );
			unset( $blob['ID'] );

			if ( $with_revision ) {
				$state_id = $this->states[] = wp_insert_post( array_merge( $blob, array(
					'post_parent' => $blob_id,
					'post_type'   => 'revision',
				) ) );

				wp_set_object_terms( $state_id, 'php', 'wpgp_language', false );
			}
		}

		if ( $with_revision ) {
			update_metadata( 'post', $this->commit->ID, '_wpgp_state_ids', $this->states );
		}

		$this->language = get_term_by( 'slug', 'php', 'wpgp_language' );

		update_post_meta( $this->repo->ID, '_wpgp_gist_id', 'none' );
		update_post_meta( $this->repo->ID, '_wpgp_sync', 'off' );
	}
}
