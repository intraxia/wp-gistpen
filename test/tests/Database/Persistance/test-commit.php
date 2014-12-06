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

		$this->persistance = new Persistance( WP_Gistpen::$plugin_name, WP_Gistpen::$version );
		$this->query = new Query( WP_Gistpen::$plugin_name, WP_Gistpen::$version );
		$this->adapter = new Adapter( WP_Gistpen::$plugin_name, WP_Gistpen::$version );

		$this->zip = $this->adapter->build( 'zip' )->blank();
		$this->file = $this->adapter->build( 'file' )->blank();
	}

	function test_save_new_commit() {
		$parent_zip = $this->query->by_id( $this->gistpen->ID );

		$revision_id = wp_save_post_revision( $this->gistpen->ID );

		foreach ( $parent_zip->get_files() as $file ) {
			$this->assertCount( 0, wp_get_post_revisions( $file->get_ID() ) );
		}

		$result = $this->persistance->by_parent_zip( $parent_zip, $revision_id );

		$this->assertInternalType( 'array', $result );
		$this->assertCount( 3, $result['files'] );

		foreach ( $result['files'] as $file_revision_id ) {
			$this->assertEquals( 'revision', get_post_type( $file_revision_id ) );
		}

		foreach ( $parent_zip->get_files() as $file ) {
			$this->assertCount( 1, wp_get_post_revisions( $file->get_ID() ) );
		}

	}

	function tearDown() {
		parent::tearDown();
	}
}
