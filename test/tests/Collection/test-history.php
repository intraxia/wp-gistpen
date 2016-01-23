<?php
use WP_Gistpen\Collection\History as HistoryCollection;
use WP_Gistpen\Model\Commit\Meta as Commit;

/**
 * @group collections
 */
class WP_Gistpen_Collection_History_Test extends WP_Gistpen_UnitTestCase {

	function setUp() {
		parent::setUp();

		$this->history = new HistoryCollection();

		$this->mock_commit
			->shouldReceive( 'get_ID' )
			->andReturn( 99 )
			->byDefault();
	}

	function test_implements_countable() {
		$this->history->add_commit( $this->mock_commit );

		$this->assertCount( 1, $this->history );
	}

	function test_get_commits() {
		$this->assertCount( 0, $this->history->get_commits() );
	}

	function test_add_commit() {
		$this->history->add_commit( $this->mock_commit );

		$this->assertCount( 1, $this->history->get_commits() );
	}

	function test_set_commits() {
		$this->history->set_commits( array( $this->mock_commit ) );

		$this->assertCount( 1, $this->history->get_commits() );
	}

	function test_get_set_description() {
		$this->history->set_head_id( '1234' );

		$this->assertEquals( 1234, $this->history->get_head_id() );
	}

	function test_get_first_commit() {
		$commit = new Commit();
		$commit->set_ID( 2 );

		$this->mock_commit
			->shouldReceive( 'get_ID' )
			->once()
			->andReturn( 1 );

		$this->history->set_commits( array( 1 => $this->mock_commit, 2 => $commit ) );

		$this->assertNotEquals( $commit, $this->history->get_first_commit() );
	}

	function test_get_last_commit() {
		$commit = new Commit();
		$commit->set_ID( 1 );

		$this->mock_commit
			->shouldReceive( 'get_ID' )
			->once()
			->andReturn( 2 );
		$this->history->set_commits( array( 1 => $commit, 2 => $this->mock_commit ) );

		$this->assertNotEquals( $commit, $this->history->get_last_commit() );
	}

	function tearDown() {
		parent::tearDown();
	}
}
