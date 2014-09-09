<?php

/**
 * @group content
 */
class WP_Gistpen_Content_Test extends WP_UnitTestCase {

	public $gistpen;
	public $gistpenfiles;

	function setUp() {
		parent::setUp();

		$this->factory = new WP_UnitTest_Factory();

		$this->gistpen = $this->factory->post->create_and_get( array(
			'post_type' => 'gistpen',
		), array(
			'post_title' => new WP_UnitTest_Generator_Sequence( 'Post title %s' ),
			'post_name' => new WP_UnitTest_Generator_Sequence( 'Post title %s' )
		));

		$this->gistpenfiles = $this->factory->post->create_many( 3, array(
			'post_type' => 'gistpen',
			'post_parent' => $this->gistpen->ID
		), array(
			'post_title' => new WP_UnitTest_Generator_Sequence( 'Post title %s' ),
			'post_name' => new WP_UnitTest_Generator_Sequence( 'Post title %s' ),
			'post_content' => new WP_UnitTest_Generator_Sequence( 'Post content %s' )
		));
	}

	function test_get_post_content() {
		$content = WP_Gistpen_Content::get_post_content( $this->gistpen );

		$sub_str_count = substr_count( $content, '<h2 class="wp-gistpenfile-title">' );
		$this->assertEquals( 3, $sub_str_count );
	}

	function test_get_shortcode_content_child() {
		$content = WP_Gistpen_Content::get_shortcode_content( array( 'id' => $this->gistpenfiles[0], 'highlight' => null ) );

		$sub_str_count = substr_count( $content, '<h2 class="wp-gistpenfile-title">' );
		$this->assertEquals( 1, $sub_str_count );
		$sub_str_count = substr_count( $content, 'Post content' );
		$this->assertEquals( 1, $sub_str_count );
		$sub_str_count = substr_count( $content, 'data-line=' );
		$this->assertEquals( 0, $sub_str_count );
	}

	function test_get_shortcode_content_parent() {
		$content = WP_Gistpen_Content::get_shortcode_content( array( 'id' => $this->gistpen->ID, 'highlight' => null ) );

		$sub_str_count = substr_count( $content, '<h2 class="wp-gistpenfile-title">' );
		$this->assertEquals( 3, $sub_str_count );
		$sub_str_count = substr_count( $content, 'Post content' );
		$this->assertEquals( 3, $sub_str_count );
		$sub_str_count = substr_count( $content, 'data-line=' );
		$this->assertEquals( 0, $sub_str_count );
	}

	function test_get_shortcode_with_highlight() {
		$content = WP_Gistpen_Content::get_shortcode_content( array( 'id' => $this->gistpenfiles[0], 'highlight' => '1' ) );

		$sub_str_count = substr_count( $content, 'data-line=' );
		$this->assertEquals( 1, $sub_str_count );
	}

	function tearDown() {
		parent::tearDown();
	}
}
