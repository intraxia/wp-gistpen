<?php
use WP_Gistpen\Facade\Database;

/**
 * @group  database
 */
class WP_Gistpen_Facade_Database_Test extends WP_Gistpen_UnitTestCase {

	function setUp() {
		parent::setUp();
		$this->database = new Database( WP_Gistpen::$plugin_name, WP_Gistpen::$version );
	}

	function test_get_head_query() {
		$query = $this->database->query( 'head' );

		$this->assertInstanceOf('WP_Gistpen\Database\Query\Head', $query );
	}

	function test_get_head_query_no_arg() {
		$query = $this->database->query();

		$this->assertInstanceOf('WP_Gistpen\Database\Query\Head', $query );
	}

	function test_get_head_persistance() {
		$persist = $this->database->persist( 'head' );

		$this->assertInstanceOf('WP_Gistpen\Database\Persistance\Head', $persist );
	}

	function test_get_head_persistance_no_arg() {
		$persist = $this->database->persist();

		$this->assertInstanceOf('WP_Gistpen\Database\Persistance\Head', $persist );
	}

	function tearDown() {
		parent::tearDown();
	}
}
