<?php
use Intraxia\Gistpen\Adapter\Api as ApiAdapter;
use Intraxia\Gistpen\Facade\Database;

/**
 * @group adapters
 */
class Adapter_Api_Test extends \Intraxia\Gistpen\Test\UnitTestCase {

	function setUp() {
		parent::setUp();

		$this->create_post_and_children();

		$this->adapter = new ApiAdapter();
		$this->database = new Database();
	}

	function test_create_api_by_zip() {
		$zip = $this->database->query( 'head' )->by_id( $this->gistpen->ID );

		$api = $this->adapter->by_zip( $zip );

		$this->assertEquals( 'Post title 1', $api->description );
		$this->assertCount( 3, $api->files );

		foreach ( $api->files as $data ) {
			$this->assertContains( 'post-title', $data->slug );
			$this->assertContains( 'Post content', $data->code );
		}
	}

	function tearDown() {
		parent::tearDown();
	}
}
