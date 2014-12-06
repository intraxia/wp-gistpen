<?php

use WP_Gistpen\Database\Persistance\Commit as Persistance;
use WP_Gistpen\Database\Query\Head as Query;
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
		$this->query = new Query( WP_Gistpen::$plugin_name, WP_Gistpen::$version );
		$this->adapter = new Adapter( WP_Gistpen::$plugin_name, WP_Gistpen::$version );
	}

	function test_save_new_commit() {
		$parent_zip = $this->query->by_id( $this->gistpen->ID );

		$result = $this->persistance->by_parent_zip( $parent_zip );

		$this->assertCount( 1, $result );

		foreach ( $result as $ID => $meta ) {
			$this->assertInternalType( 'array', $meta );
			$this->assertInternalType( 'int', $ID );
			$this->assertCount( 3, $meta['files'] );

			foreach ( $meta['files'] as $file_revision_id ) {
				$this->assertEquals( 'revision', get_post_type( $file_revision_id ) );
			}
		}

		foreach ( $parent_zip->get_files() as $file ) {
			$this->assertCount( 1, wp_get_post_revisions( $file->get_ID() ) );
		}

	}

	function tearDown() {
		parent::tearDown();
	}
}
