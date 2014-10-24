<?php
use WP_Gistpen\Content;

/**
 * @group content
 */
class WP_Gistpen_Content_Test extends WP_Gistpen_UnitTestCase {

	public $gistpen;
	public $gistpenfiles;

	function setUp() {
		parent::setUp();

		$this->create_post_and_children();
		$this->content = new Content( WP_Gistpen::$plugin_name, WP_Gistpen::$version );
	}

	function test_get_post_content() {
		global $post;

		$post = $this->gistpen;
		$content = $this->content->post_content();

		$sub_str_count = substr_count( $content, '<h3 class="wp-gistpenfile-title">' );
		$this->assertEquals( 3, $sub_str_count );
	}

	function tearDown() {
		parent::tearDown();
	}
}
