<?php
use WP_Gistpen\Adapter\Gist as GistAdapter;
use WP_Gistpen\Facade\Database;

class WP_Gistpen_Adapter_Gist_Test extends WP_Gistpen_UnitTestCase {

	function setUp() {
		parent::setUp();

		$this->create_post_and_children();

		$this->adapter = new GistAdapter( WP_Gistpen::$plugin_name, WP_Gistpen::$version );
		$this->database = new Database( WP_Gistpen::$plugin_name, WP_Gistpen::$version );
	}

	function test_create_gist_by_zip() {
		$zip = $this->database->query( 'head' )->by_id( $this->gistpen->ID );

		$gist = $this->adapter->create_by_zip( $zip );

		$this->assertEquals( 'Post title 1', $gist['description'] );
		$this->assertTrue( $gist['public'] );
		$this->assertCount( 3, $gist['files'] );

		foreach ( $gist['files'] as $filename => $data ) {
			$this->assertContains( 'post-title', $filename );
			$this->assertContains( 'Post content', $data['content'] );
		}
	}

	function tearDown() {
		parent::tearDown();
	}
}
