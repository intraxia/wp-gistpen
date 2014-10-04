<?php

/**
 * @group objects
 * @group post
 */
class WP_Gistpen_Post_Test extends WP_Gistpen_UnitTestCase {

	public $post_obj;
	public $post;
	public $file_obj;
	public $file;

	function setUp() {
		parent::setUp();
		$this->post_obj = $this->factory->gistpen->create_and_get();
	}

	function test_doesnt_need_files() {
		$this->post = new WP_Gistpen_Post( $this->post_obj );

		$this->assertInstanceOf( 'WP_Gistpen_Post', $this->post );
	}

	function test_fail_if_files_not_array() {
		$this->setExpectedException( 'Exception', "Files must be in an array" );

		$this->post = new WP_Gistpen_Post( $this->post_obj, 'wrong' );
	}

	function test_description_equals_post_title() {
		$this->post = new WP_Gistpen_Post( $this->post_obj );

		$this->assertEquals( $this->post_obj->post_title, $this->post->description );
	}

	function test_files_returns_array() {
		$this->post = new WP_Gistpen_Post( $this->post_obj );

		$this->assertInternalType( 'array', $this->post->files );
	}

	function test_post_content_has_file_content() {
		$this->post = new WP_Gistpen_Post( $this->post_obj, array( new WP_Gistpen_File( $this->factory->gistpen->create_and_get() , $this->mock_lang ) ) );

		$this->assertContains( 'Post content', $this->post->post_content );
	}

	function test_shortcode_content_has_file_content() {
		$this->post = new WP_Gistpen_Post( $this->post_obj, array( new WP_Gistpen_File( $this->factory->gistpen->create_and_get() , $this->mock_lang ) ) );

		$this->assertContains( 'Post content', $this->post->shortcode_content );
	}

	function test_update_post() {
		$this->mock_file
			->expects($this->once())
			->method('update_post')
			->will($this->returnValue(true));
		$this->post = new WP_Gistpen_Post( $this->post_obj, array( $this->mock_file ) );

		$this->post->description = "New description";

		$this->post->update_post();

		$this->assertEquals( "New description", $this->post->post->post_title );
	}

	function tearDown() {
		parent::tearDown();
	}
}
