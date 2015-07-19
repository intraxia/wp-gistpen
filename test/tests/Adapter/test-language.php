<?php
use WP_Gistpen\Adapter\Language as LanguageAdapter;

/**
 * @group adapters
 */
class WP_Gistpen_LanguageAdapter_Test extends WP_Gistpen_UnitTestCase {

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
