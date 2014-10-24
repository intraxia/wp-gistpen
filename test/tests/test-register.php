<?php
use WP_Gistpen\Register;

/**
 * @group register
 */
class WP_Gistpen_Register_Test extends WP_Gistpen_UnitTestCase {

	function setUp() {
		parent::setUp();

		$this->create_post_and_children();
		$this->register = new Register( WP_Gistpen::$plugin_name, WP_Gistpen::$version );;
	}

	function test_get_shortcode_content_child() {
		$content = $this->register->add_shortcode( array( 'id' => $this->files[0], 'highlight' => null ) );

		$sub_str_count = substr_count( $content, '<h3 class="wp-gistpenfile-title">' );
		$this->assertEquals( 1, $sub_str_count );
		$sub_str_count = substr_count( $content, 'Post content' );
		$this->assertEquals( 1, $sub_str_count );
		$sub_str_count = substr_count( $content, 'data-line=' );
		$this->assertEquals( 0, $sub_str_count );
	}

	function test_get_shortcode_content_parent() {
		$content = $this->register->add_shortcode( array( 'id' => $this->gistpen->ID, 'highlight' => null ) );

		$sub_str_count = substr_count( $content, '<h3 class="wp-gistpenfile-title">' );
		$this->assertEquals( 3, $sub_str_count );
		$sub_str_count = substr_count( $content, 'Post content' );
		$this->assertEquals( 3, $sub_str_count );
		$sub_str_count = substr_count( $content, 'data-line=' );
		$this->assertEquals( 0, $sub_str_count );
	}

	function test_get_shortcode_with_highlight() {
		$content = $this->register->add_shortcode( array( 'id' => $this->files[0], 'highlight' => '1' ) );

		$sub_str_count = substr_count( $content, 'data-line=' );
		$this->assertEquals( 1, $sub_str_count );
	}

	function tearDown() {
		parent::tearDown();
	}
}
