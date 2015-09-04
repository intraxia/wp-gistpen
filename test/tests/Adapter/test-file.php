<?php
use Intraxia\Gistpen\Adapter\File as FileAdapter;

/**
 * @group adapters
 */
class FileAdapter_Test extends \Intraxia\Gistpen\Test\UnitTestCase {

	function setUp() {
		parent::setUp();

		$this->adapter = new FileAdapter();
	}

	function test_build_by_array_complete() {
		$data = array(
			'slug' => 'test-this',
			'code' => 'echo $stuff;',
			'ID'   => 123
		);

		$file = $this->adapter->by_array( $data );

		$this->assertEquals( 'test-this', $file->get_slug() );
		$this->assertEquals( 'echo $stuff;', $file->get_code() );
		$this->assertEquals( 123, $file->get_ID() );
	}

	function test_build_by_array_with_extra_vars() {
		$data = array(
			'slug'  => 'test-this',
			'code'  => 'echo $stuff;',
			'ID'    => 123,
			'extra' => 'stuff'
		);

		$file = $this->adapter->by_array( $data );

		$this->assertEquals( 'test-this', $file->get_slug() );
		$this->assertEquals( 'echo $stuff;', $file->get_code() );
		$this->assertEquals( 123, $file->get_ID() );
	}

	function test_build_by_array_with_only_ID() {
		$data = array(
			'ID'    => 123
		);

		$file = $this->adapter->by_array( $data );

		$this->assertEquals( '', $file->get_slug() );
		$this->assertEquals( '', $file->get_code() );
		$this->assertEquals( 123, $file->get_ID() );
	}

	function test_build_by_array_with_only_code() {
		$data = array(
			'code'  => 'echo $stuff;'
		);

		$file = $this->adapter->by_array( $data );

		$this->assertEquals( '', $file->get_slug() );
		$this->assertEquals( 'echo $stuff;', $file->get_code() );
		$this->assertEquals( null, $file->get_ID() );
	}

	function test_build_by_array_with_only_slug() {
		$data = array(
			'slug'  => 'test-this'
		);

		$file = $this->adapter->by_array( $data );

		$this->assertEquals( 'test-this', $file->get_slug() );
		$this->assertEquals( '', $file->get_code() );
		$this->assertEquals( null, $file->get_ID() );
	}

	function test_build_by_post() {
		$post = new WP_Post( new stdClass );
		$post->post_content = 'echo $stuff;';
		$post->post_title = 'Test This';
		$post->ID = 123;

		$file = $this->adapter->by_post( $post );

		$this->assertEquals( 'test-this', $file->get_slug() );
		$this->assertEquals( 'echo $stuff;', $file->get_code() );
		$this->assertEquals( 123, $file->get_ID() );
	}

	function tearDown() {
		parent::tearDown();
	}
}
