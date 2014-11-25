<?php
use WP_Gistpen\Facade\Database;

/**
 * @group  Database
 */
class WP_Gistpen_Facade_Database_Test extends WP_Gistpen_UnitTestCase {

	function setUp() {
		parent::setUp();
		$this->database = new Database( WP_Gistpen::$plugin_name, WP_Gistpen::$version );
	}

	function test_get_query() {
		$query = $this->database->query();

		$this->assertInstanceOf('WP_Gistpen\Database\Query', $query );
	}

	function test_get_language_Database() {
		$persist = $this->database->persist();

		$this->assertInstanceOf('WP_Gistpen\Database\Persistance', $persist );
	}

	function tearDown() {
		parent::tearDown();
	}
}
