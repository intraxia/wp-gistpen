<?php

use WP_Gistpen\Database\Query;
use WP_Gistpen\Model\Zip;
use WP_Gistpen\Model\File;
use WP_Gistpen\Model\Language;

/**
 * @group models
 */
class WP_Gistpen_Model_Language_Test extends WP_Gistpen_UnitTestCase {

	public $language;

	function setUp() {
		parent::setUp();
		$this->language = new Language( WP_Gistpen::$plugin_name, WP_Gistpen::$version, 'php' );
	}

	function test_inst_fail_non_existant_language_slug() {
		$this->setExpectedException('Exception');

		$language = new Language( WP_Gistpen::$plugin_name, WP_Gistpen::$version, 'slug' );
	}

	function test_get_slug() {
		$this->assertEquals( 'php', $this->language->get_slug() );
	}

	function test_return_prism_slug() {
		$this->assertEquals( 'php', $this->language->get_prism_slug() );
	}

	function test_fix_prism_slug_javascript() {
		$language = new Language( WP_Gistpen::$plugin_name, WP_Gistpen::$version,'js' );

		$this->assertEquals( 'javascript', $language->get_prism_slug() );
	}

	function test_fix_prism_slug_sass() {
		$language = new Language( WP_Gistpen::$plugin_name, WP_Gistpen::$version,'sass' );

		$this->assertEquals( 'scss', $language->get_prism_slug() );
	}

	function test_return_file_ext() {
		$this->assertEquals( 'php', $this->language->get_file_ext() );
	}

	function test_fix_file_ext_bash() {
		$language = new Language( WP_Gistpen::$plugin_name, WP_Gistpen::$version,'bash' );

		$this->assertEquals( 'sh', $language->get_file_ext() );
	}

	function test_fix_file_ext_sass() {
		$language = new Language( WP_Gistpen::$plugin_name, WP_Gistpen::$version,'sass' );

		$this->assertEquals( 'scss', $language->get_file_ext() );
	}

	function test_return_display_name() {
		$this->assertEquals( 'PHP', $this->language->get_display_name() );
	}

	function test_set_fail_non_existant_language_slug() {
		$this->setExpectedException('Exception');

		$this->language->set_slug( 'slug' );
	}

	function test_set_slug() {
		$this->language->set_slug( 'ruby' );

		$this->assertEquals( 'ruby', $this->language->get_slug() );
	}

	function tearDown() {
		parent::tearDown();
	}
}
