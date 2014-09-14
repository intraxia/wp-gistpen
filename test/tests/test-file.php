<?php

/**
 * @group objects
 */
class WP_Gistpen_File_Test extends WP_Gistpen_UnitTestCase {

	public $file_obj;
	public $file;

	function setUp() {
		parent::setUp();
		$this->file_obj = $this->factory->gistpen->create_and_get( array( 'post_parent' => $this->factory->gistpen->create() ) );
		$this->file = new WP_Gistpen_File( $this->file_obj, $this->mock_lang, $this->mock_post  );

		$this->mock_lang
			->expects( $this->any() )
			->method( '__get' )
			->with( $this->anything() );
	}

	function test_get_post_object() {
		$this->assertInstanceOf('WP_Post', $this->file->file);
	}

	function test_get_parent_post_object() {
		$this->assertEquals( $this->mock_post, $this->file->parent );
	}

	function test_get_slug() {
		$this->assertContains( 'post-title', $this->file->slug );
	}

	function test_get_filename_with_extension() {
		$this->assertContains( 'post-title', $this->file->filename );
		$this->assertContains( '.', $this->file->filename );
		$this->assertNotContains( ' ', $this->file->filename );
	}

	function test_get_code() {
		$this->assertContains( 'Post content', $this->file->code );
	}

	function test_get_post_content() {
		$this->assertTag( array(
			'tag' => 'div',
			'id' => 'wp-gistpenfile-' . $this->file_obj->post_name
		), $this->file->post_content );
	}

	function test_get_shortcode_content() {
		$this->assertTag( array(
			'tag' => 'div',
			'id' => 'wp-gistpenfile-' . $this->file_obj->post_name
		), $this->file->post_content );
	}

	function tearDown() {
		parent::tearDown();
	}
}
