<?php
use Intraxia\Gistpen\Adapter\Language as LanguageAdapter;

/**
 * @group adapters
 */
class LanguageAdapter_Test extends \Intraxia\Gistpen\Test\UnitTestCase {

	function setUp() {
		parent::setUp();
		$this->adapter = new LanguageAdapter();
	}

	function test_build_by_slug() {
		$language = $this->adapter->by_slug( 'php' );

		$this->assertEquals( 'php', $language->get_slug() );
	}

	function test_build_blank() {
		$language = $this->adapter->blank();

		$this->assertEmpty( $language->get_slug() );
	}

	function tearDown() {
		parent::tearDown();
	}
}
