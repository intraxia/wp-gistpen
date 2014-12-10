<?php
use WP_Gistpen\Account\Gist;

/**
 * @group account
 */
class WP_Gistpen_Account_Gist_Test extends WP_Gistpen_UnitTestCase {

	function setUp() {
		parent::setUp();

		$this->gist = new Gist( WP_Gistpen::$plugin_name, WP_Gistpen::$version );
	}

	function test_sample() {
		$this->markTestIncomplete( "This test is incomplete." );
	}

	function tearDown() {
		parent::tearDown();
	}
}
