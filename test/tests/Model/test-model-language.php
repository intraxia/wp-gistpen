<?php

use Intraxia\Gistpen\Database\Query;
use Intraxia\Gistpen\Model\Zip;
use Intraxia\Gistpen\Model\File;
use Intraxia\Gistpen\Model\Language;

/**
 * @group models
 */
class Model_Language_Test extends \Intraxia\Gistpen\Test\UnitTestCase {

	public $language;

	function setUp() {
		parent::setUp();
		$this->language = new Language( 'php' );
	}

	function test_inst_fail_non_existant_language_slug() {
		$this->setExpectedException('Exception');

		$language = new Language( 'slug' );
	}

	function test_get_slug() {
		$this->assertEquals( 'php', $this->language->get_slug() );
	}

	function test_return_prism_slug() {
		$this->assertEquals( 'php', $this->language->get_prism_slug() );
	}

	function test_fix_prism_slug_javascript() {
		$language = new Language( 'js' );

		$this->assertEquals( 'javascript', $language->get_prism_slug() );
	}

	function test_fix_prism_slug_sass() {
		$language = new Language( 'sass' );

		$this->assertEquals( 'scss', $language->get_prism_slug() );
	}

	function test_return_file_ext() {
		$this->assertEquals( 'php', $this->language->get_file_ext() );
	}

	function test_fix_file_ext_bash() {
		$language = new Language( 'bash' );

		$this->assertEquals( 'sh', $language->get_file_ext() );
	}

	function test_fix_file_ext_sass() {
		$language = new Language( 'sass' );

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
