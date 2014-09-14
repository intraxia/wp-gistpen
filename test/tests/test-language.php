<?php

/**
 * @group objects
 */
class WP_Gistpen_Language_Test extends WP_Gistpen_UnitTestCase {

	public $language;

	function setUp() {
		parent::setUp();
	}

	function test_get_slug() {
		$term = new stdClass;
		$term->slug = 'slug';
		$this->language = new WP_Gistpen_Language( $term );

		$this->assertEquals( 'slug', $this->language->slug );
	}

	function test_return_prism_slug() {
		$term = new stdClass;
		$term->slug = 'slug';
		$this->language = new WP_Gistpen_Language( $term );

		$this->assertEquals( 'slug', $this->language->prism_slug );
	}

	function test_fix_prism_slug_javascript() {
		$term = new stdClass;
		$term->slug = 'js';
		$this->language = new WP_Gistpen_Language( $term );

		$this->assertEquals( 'javascript', $this->language->prism_slug );
	}

	function test_fix_prism_slug_sass() {
		$term = new stdClass;
		$term->slug = 'sass';
		$this->language = new WP_Gistpen_Language( $term );

		$this->assertEquals( 'scss', $this->language->prism_slug );
	}

	function test_return_file_ext() {
		$term = new stdClass;
		$term->slug = 'slug';
		$this->language = new WP_Gistpen_Language( $term );

		$this->assertEquals( 'slug', $this->language->file_ext );
	}

	function test_fix_file_ext_bash() {
		$term = new stdClass;
		$term->slug = 'bash';
		$this->language = new WP_Gistpen_Language( $term );

		$this->assertEquals( 'sh', $this->language->file_ext );
	}

	function test_fix_file_ext_sass() {
		$term = new stdClass;
		$term->slug = 'sass';
		$this->language = new WP_Gistpen_Language( $term );

		$this->assertEquals( 'scss', $this->language->file_ext );
	}

	function test_return_display_name() {
		$term = new stdClass;
		$term->name = 'Language name';
		$this->language = new WP_Gistpen_Language( $term );

		$this->assertEquals( 'Language name', $this->language->display_name );
	}

	function tearDown() {
		parent::tearDown();
	}
}
