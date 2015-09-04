<?php

use Intraxia\Gistpen\Database\Query\Commit as Query;
use Intraxia\Gistpen\Model\Zip;

/**
 * @group database
 */
class Database_Query_Commit_Test extends \Intraxia\Gistpen\Test\UnitTestCase {

	public $query;

	function setUp() {
		parent::setUp();

		$this->create_post_and_children();

		$this->query = new Query();

		$migration = new Intraxia\Gistpen\Migration();
		delete_post_meta( $this->gistpen->ID, 'wpgp_revisions' );
		$migration->update_to_0_5_0();
	}

	function test_get_commits() {
		$revisions = $this->query->history_by_head_id( $this->gistpen->ID );

		$this->assertCount( 1, $revisions );

		foreach ( $revisions->get_commits() as $commit ) {
			$this->assertInstanceOf( 'Intraxia\Gistpen\Model\Commit\Meta', $commit );
            $this->assertEquals('none', $commit->getGistSha());
			$this->assertEquals( 'none', $commit->get_gist_id() );
		}
	}

	function tearDown() {
		parent::tearDown();
	}
}
