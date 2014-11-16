<?php

use WP_Gistpen\Database\Query;
use WP_Gistpen\Model\Zip;
use WP_Gistpen\Model\File;
use WP_Gistpen\Model\Language;

/**
 * @group objects
 * @group post
 */
class WP_Gistpen_Zip_Test extends WP_Gistpen_UnitTestCase {

	public $zip_post;
	public $zip;
	public $file_obj;
	public $file;

	function setUp() {
		parent::setUp();
		$this->zip_post = $this->factory->gistpen->create_and_get();
	}

	function test_doesnt_need_files() {
		$this->zip = new Zip( $this->zip_post );

		$this->assertInstanceOf( 'WP_Gistpen\Model\Zip', $this->zip );
	}

	function test_fail_if_files_not_array() {
		$this->setExpectedException( 'Exception', "Files must be in an array" );

		$this->zip = new Zip( $this->zip_post, 'wrong' );
	}

	function test_description_equals_post_title() {
		$this->zip = new Zip( $this->zip_post );

		$this->assertEquals( $this->zip_post->post_title, $this->zip->description );
	}

	function test_files_returns_array() {
		$this->zip = new Zip( $this->zip_post );

		$this->assertInternalType( 'array', $this->zip->files );
	}

	function test_post_content_has_file_content() {
		$this->zip = new Zip( $this->zip_post, array( new File( $this->factory->gistpen->create_and_get() , $this->mock_lang ) ) );

		$this->assertContains( 'Post content', $this->zip->post_content );
	}

	function test_shortcode_content_has_file_content() {
		$this->zip = new Zip( $this->zip_post, array( new File( $this->factory->gistpen->create_and_get() , $this->mock_lang ) ) );

		$this->assertContains( 'Post content', $this->zip->shortcode_content );
	}

	function test_update_post() {
		$this->mock_file
			->expects($this->once())
			->method('update_post')
			->will($this->returnValue(true));
		$this->zip = new Zip( $this->zip_post, array( $this->mock_file ) );

		$this->zip->description = "New description";

		$this->zip->update_post();

		$this->assertEquals( "New description", $this->zip->post->post_title );
	}

	function tearDown() {
		parent::tearDown();
	}
}
