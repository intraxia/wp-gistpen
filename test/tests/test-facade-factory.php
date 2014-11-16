<?php
use WP_Gistpen\Facade\Factory;

/**
 * @group  factory
 */
class WP_Gistpen_Facade_Factory_Test extends WP_Gistpen_UnitTestCase {

	function setUp() {
		parent::setUp();
		$this->factory = new Factory( WP_Gistpen::$plugin_name, WP_Gistpen::$version );
	}

	function test_get_file_factory() {
		$file = $this->factory->build( 'file' );

		$this->assertInstanceOf('WP_Gistpen\Factory\File', $file );
	}

	function test_get_language_factory() {
		$language = $this->factory->build( 'language' );

		$this->assertInstanceOf('WP_Gistpen\Factory\Language', $language );
	}

	function test_get_zip_factory() {
		$zip = $this->factory->build( 'zip' );

		$this->assertInstanceOf('WP_Gistpen\Factory\Zip', $zip );
	}

	function test_fail_everything_else() {
		$this->setExpectedException('Exception');
		$crap = $this->factory->build( 'crap' );
	}

	function tearDown() {
		parent::tearDown();
	}
}
