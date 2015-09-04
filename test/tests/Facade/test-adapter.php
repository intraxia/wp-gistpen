<?php
use Intraxia\Gistpen\Facade\Adapter;

/**
 * @group  facades
 */
class Facade_Adapter_Test extends \Intraxia\Gistpen\Test\UnitTestCase {

	function setUp() {
		parent::setUp();
		$this->adapter = new Adapter();
	}

	function test_get_file_adapter() {
		$file = $this->adapter->build( 'file' );

		$this->assertInstanceOf('Intraxia\Gistpen\Adapter\File', $file );
	}

	function test_get_language_adapter() {
		$language = $this->adapter->build( 'language' );

		$this->assertInstanceOf('Intraxia\Gistpen\Adapter\Language', $language );
	}

	function test_get_zip_adapter() {
		$zip = $this->adapter->build( 'zip' );

		$this->assertInstanceOf('Intraxia\Gistpen\Adapter\Zip', $zip );
	}

	function test_get_json_adapter() {
		$api = $this->adapter->build( 'api' );

		$this->assertInstanceOf('Intraxia\Gistpen\Adapter\Api', $api );
	}

	function test_fail_everything_else() {
		$this->setExpectedException('Exception');

		$crap = $this->adapter->build( 'crap' );
	}

	function tearDown() {
		parent::tearDown();
	}
}
