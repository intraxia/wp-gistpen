<?php
use WP_Gistpen\Adapter\JSON as JSONAdapter;
use WP_Gistpen\Facade\Database;

/**
 * @group adapter
 */
class WP_Gistpen_Adapter_JSON_Test extends WP_Gistpen_UnitTestCase {

	function setUp() {
		parent::setUp();

		$this->create_post_and_children();

		$this->adapter = new JSONAdapter( WP_Gistpen::$plugin_name, WP_Gistpen::$version );
		$this->database = new Database( WP_Gistpen::$plugin_name, WP_Gistpen::$version );
	}

	function test_create_json_by_zip() {
		$zip = $this->database->query( 'head' )->by_id( $this->gistpen->ID );

		$json = $this->adapter->by_zip( $zip );

		$json_obj = json_decode($json);
		$this->assertTrue( ( json_last_error() == JSON_ERROR_NONE ), 'String is not valid json' );

		$this->assertEquals( 'Post title 1', $json_obj->zip->description );
		$this->assertCount( 3, $json_obj->files );

		foreach ( $json_obj->files as $data ) {
			$this->assertContains( 'post-title', $data->slug );
			$this->assertContains( 'Post content', $data->code );
		}
	}

	function tearDown() {
		parent::tearDown();
	}
}
