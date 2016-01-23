<?php
use WP_Gistpen\Facade\Adapter;

/**
 * @group  facades
 */
class WP_Gistpen_Facade_Adapter_Test extends WP_Gistpen_UnitTestCase {

	function setUp() {
		parent::setUp();
		$this->adapter = new Adapter();
	}

	function test_get_file_adapter() {
		$file = $this->adapter->build( 'file' );

		$this->assertInstanceOf('WP_Gistpen\Adapter\File', $file );
	}

	function test_get_language_adapter() {
		$language = $this->adapter->build( 'language' );

		$this->assertInstanceOf('WP_Gistpen\Adapter\Language', $language );
	}

	function test_get_zip_adapter() {
		$zip = $this->adapter->build( 'zip' );

		$this->assertInstanceOf('WP_Gistpen\Adapter\Zip', $zip );
	}

	function test_get_gist_adapter() {
		$gist = $this->adapter->build( 'gist' );

		$this->assertInstanceOf('WP_Gistpen\Adapter\Gist', $gist );
	}

	function test_get_json_adapter() {
		$api = $this->adapter->build( 'api' );

		$this->assertInstanceOf('WP_Gistpen\Adapter\Api', $api );
	}

	function test_fail_everything_else() {
		$this->setExpectedException('Exception');

		$crap = $this->adapter->build( 'crap' );
	}

	function tearDown() {
		parent::tearDown();
	}
}
