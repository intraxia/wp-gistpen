<?php

use WP_Gistpen\Database\Query\Commit as Query;
use WP_Gistpen\Facade\Database;
use WP_Gistpen\Model\Zip;
use WP_Gistpen\Model\File;
use WP_Gistpen\Model\Language;

/**
 * @group database
 */
class WP_Gistpen_Database_Query_Commit_Test extends WP_Gistpen_UnitTestCase {

	public $query;

	function setUp() {
		parent::setUp();

		$this->create_post_and_children();

		$this->database = new Database( WP_Gistpen::$plugin_name, WP_Gistpen::$version );
		$parent_zip = $this->database->query()->by_id( $this->gistpen->ID );
		$revisions_meta = array();
		for ($i=1; $i < 3; $i++) {
			$result = $this->database->persist( 'commit' )->by_parent_zip( $parent_zip );
			$revisions_meta[ $result['ID'] ] = $result['meta'];
		}
		update_post_meta( $this->gistpen->ID, 'wpgp_revisions', $revisions_meta );

		$this->query = new Query( WP_Gistpen::$plugin_name, WP_Gistpen::$version );
	}

	function test_get_revisions() {
		$revisions = $this->query->all_by_parent_id( $this->gistpen->ID );

		$this->assertCount( 2, $revisions );

		foreach ( $revisions as $revision ) {
			$this->assertInstanceOf( 'WP_Gistpen\Model\Zip', $revision );
		}
	}

	function tearDown() {
		parent::tearDown();
	}
}
