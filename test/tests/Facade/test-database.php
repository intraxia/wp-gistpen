<?php
use Intraxia\Gistpen\Facade\Database;

/**
 * @group  facades
 */
class Facade_Database_Test extends \Intraxia\Gistpen\Test\UnitTestCase {

	function setUp() {
		parent::setUp();
		$this->database = new Database();
	}

	function test_get_head_query() {
		$query = $this->database->query( 'head' );

		$this->assertInstanceOf('Intraxia\Gistpen\Database\Query\Head', $query );
	}

	function test_get_head_query_no_arg() {
		$query = $this->database->query();

		$this->assertInstanceOf('Intraxia\Gistpen\Database\Query\Head', $query );
	}

	function test_get_commit_query() {
		$query = $this->database->query( 'commit' );

		$this->assertInstanceOf('Intraxia\Gistpen\Database\Query\Commit', $query );
	}

	function test_get_head_persistance() {
		$persist = $this->database->persist( 'head' );

		$this->assertInstanceOf('Intraxia\Gistpen\Database\Persistance\Head', $persist );
	}

	function test_get_head_persistance_no_arg() {
		$persist = $this->database->persist();

		$this->assertInstanceOf('Intraxia\Gistpen\Database\Persistance\Head', $persist );
	}

	function test_get_commit_persistance() {
		$persist = $this->database->persist( 'commit' );

		$this->assertInstanceOf('Intraxia\Gistpen\Database\Persistance\Commit', $persist );
	}

	function tearDown() {
		parent::tearDown();
	}
}
