<?php
use WP_Gistpen\Facade\Adapter;

/**
 * @group  adapter
 */
class WP_Gistpen_Facade_Adapter_Test extends WP_Gistpen_UnitTestCase {

	function setUp() {
		parent::setUp();
		$this->adapter = new Adapter( WP_Gistpen::$plugin_name, WP_Gistpen::$version );
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

	function test_fail_everything_else() {
		$this->setExpectedException('Exception');

		$crap = $this->adapter->build( 'crap' );
	}

	function tearDown() {
		parent::tearDown();
	}
}
