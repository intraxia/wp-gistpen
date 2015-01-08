<?php

use WP_Gistpen\Database\Persistance\Commit as Persistance;
use WP_Gistpen\Database\Query\Commit as CommitQuery;
use WP_Gistpen\Database\Query\Head as HeadQuery;
use WP_Gistpen\Facade\Adapter;

/**
 * @group  database
 */
class WP_Gistpen_Persistance_Commit_Test extends WP_Gistpen_UnitTestCase {

	function setUp() {
		parent::setUp();

		$this->_setRole( 'administrator' );
		$this->create_post_and_children();

		$posts = get_posts( array(
			'post_type' => 'revision',
			'post_status' => 'any',
			'nopaging' => 'true',
		));

		foreach ( $posts as $post ) {
			if ( get_post_type( $post->post_parent ) === 'gistpen' ) {
				wp_delete_post( $post->ID, true );
			}
		}

		delete_post_meta( $this->gistpen->ID, 'wpgp_revisions' );

		$this->persistance = new Persistance( WP_Gistpen::$plugin_name, WP_Gistpen::$version );
		$this->head_query = new HeadQuery( WP_Gistpen::$plugin_name, WP_Gistpen::$version );
		$this->commit_query = new CommitQuery( WP_Gistpen::$plugin_name, WP_Gistpen::$version );
		$this->adapter = new Adapter( WP_Gistpen::$plugin_name, WP_Gistpen::$version );
	}

	function test_save_new_commit() {
		$ids = array(
			'zip'   => $this->gistpen->ID,
			'files' => $this->files,
		);

		$result = $this->persistance->by_ids( $ids );

		$history = $this->commit_query->history_by_head_id( $this->gistpen->ID );

		$this->assertCount( 1, $history );
	}

	function tearDown() {
		parent::tearDown();
	}
}
