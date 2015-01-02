<?php

use WP_Gistpen\Database\Query\Commit as Query;
use WP_Gistpen\Model\Zip;

/**
 * @group database
 */
class WP_Gistpen_Database_Query_Commit_Test extends WP_Gistpen_UnitTestCase {

	public $query;

	function setUp() {
		parent::setUp();

		$this->create_post_and_children();

		$this->query = new Query( WP_Gistpen::$plugin_name, WP_Gistpen::$version );

		$migration = new WP_Gistpen\Migration( WP_Gistpen::$plugin_name, WP_Gistpen::$version );
		delete_post_meta( $this->gistpen->ID, 'wpgp_revisions' );
		$migration->update_to_0_5_0();
	}

	function test_get_commits() {
		$revisions = $this->query->history_by_head_id( $this->gistpen->ID );

		$this->assertCount( 1, $revisions );

		foreach ( $revisions as $revision ) {
			$this->assertInstanceOf( 'WP_Gistpen\Model\Zip', $revision );
		}
	}

	function tearDown() {
		parent::tearDown();
	}
}
