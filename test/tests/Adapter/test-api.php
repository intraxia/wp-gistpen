<?php
use WP_Gistpen\Adapter\Api as ApiAdapter;
use WP_Gistpen\Facade\Database;

/**
 * @group adapters
 */
class WP_Gistpen_Adapter_Api_Test extends WP_Gistpen_UnitTestCase {

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
