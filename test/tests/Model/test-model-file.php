<?php

use Intraxia\Gistpen\Database\Query;
use Intraxia\Gistpen\Model\Zip;
use Intraxia\Gistpen\Model\File;
use Intraxia\Gistpen\Model\Language;

/**
 * @group models
 */
class Model_File_Test extends \Intraxia\Gistpen\Test\UnitTestCase {

	public $file_obj;
	public $file;

	function setUp() {
		parent::setUp();
		$this->file = new File();
	}

	function test_get_set_slug() {
		$this->file->set_slug( 'post-title' );

		$this->assertContains( 'post-title', $this->file->get_slug() );
	}

	function test_get_set_code() {
		$this->file->set_code( 'Post content' );

		$this->assertContains( 'Post content', $this->file->get_code() );
	}

	function test_get_set_ID() {
		$this->file->set_ID( '123' );

		$this->assertInternalType( 'integer', $this->file->get_ID() );
		$this->assertEquals( 123, $this->file->get_ID() );
	}

	function test_get_set_language() {
		$this->file->set_language( $this->mock_lang );

		$this->assertEquals( $this->mock_lang, $this->file->get_language() );
	}

	function test_must_be_language_object() {
		$this->setExpectedException('Exception');

		$this->file->set_language( 'string' );
	}

	function test_get_filename() {
		$this->file->set_slug( 'post-title' );
		$this->file->set_language( $this->mock_lang );

		// $this->mock_lang
		// 	->shouldReceive( 'get_file_ext' )
		// 	->once()
		// 	->andReturn( 'js' );

		$filename = $this->file->get_filename();

		$this->assertContains( 'post-title', $filename );
		$this->assertNotContains( ' ', $filename );
	}

	function test_get_post_content() {
		$this->file->set_slug( 'post-title' );
		$this->file->set_code( 'echo $stuff;');
		$this->file->set_language( $this->mock_lang );

		$this->mock_lang
			// ->shouldReceive( 'get_file_ext' )
			// ->once()
			// ->andReturn( 'js' )
			->shouldReceive( 'get_prism_slug' )
			->once()
			->andReturn( 'javascript' );

		$post_content = $this->file->get_post_content();

		$this->assertValidHtml( $post_content );
		$this->assertContains( $this->file->get_code(), $post_content );
		$this->assertContains( $this->file->get_code(), $post_content );
	}

	function test_get_shortcode_content() {
		$this->file->set_slug( 'post-title' );
		$this->file->set_code( 'echo $stuff;');
		$this->file->set_language( $this->mock_lang );

		$this->mock_lang
			// ->shouldReceive( 'get_file_ext' )
			// ->once()
			// ->andReturn( 'js' )
			->shouldReceive( 'get_prism_slug' )
			->once()
			->andReturn( 'javascript' );

		$shortcode_content = $this->file->get_shortcode_content();

		$this->assertValidHtml( $shortcode_content );
		$this->assertContains( $this->file->get_code(), $shortcode_content );
		$this->assertContains( $this->file->get_code(), $shortcode_content );
	}

	// function test_update_post() {
	// 	$this->file->slug = 'New slug';
	// 	$this->file->code = 'echo $code';
	// 	$this->mock_lang
	// 		->expects($this->once())
	// 		->method('update_post')
	// 		->will($this->returnValue(true));

	// 	$this->file->update_post();

	// 	$this->assertEquals( 'new-slug', $this->file->file->post_name );
	// 	$this->assertEquals( 'echo $code', $this->file->file->post_content );
	// }

	function tearDown() {
		parent::tearDown();
	}
}
