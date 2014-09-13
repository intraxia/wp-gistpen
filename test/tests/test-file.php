<?php

/**
 * @group objects
 */
class WP_Gistpen_File_Test extends WP_Gistpen_UnitTestCase {

	public $file_obj;
	public $language;
	public $gistpen;
	public $file;

	function setUp() {
		parent::setUp();
		$this->file_obj = $this->factory->gistpen->create_and_get( array( 'post_parent' => $this->factory->gistpen->create() ) );
		$this->language = $this->getMockBuilder( 'WP_Gistpen_Language' )->disableOriginalConstructor()->getMock();
		$this->gistpen = $this->getMockBuilder( 'WP_Gistpen_Post' )->disableOriginalConstructor()->getMock();
		$this->file = new WP_Gistpen_File( $this->file_obj, $this->language, $this->gistpen  );

		$this->language
			->expects( $this->any() )
			->method( '__get' )
			->with( $this->anything() );
	}

	function test_get_post_object() {
		$this->assertInstanceOf('WP_Post', $this->file->file);
	}

	function test_get_parent_post_object() {
		$this->assertEquals( $this->gistpen, $this->file->parent );
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
