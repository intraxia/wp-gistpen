<?php
use WP_Gistpen\Adapter\Zip as ZipAdapter;

/**
 * @group adapters
 */
class WP_Gistpen_ZipAdapter_Test extends WP_Gistpen_UnitTestCase {

	function setUp() {
		parent::setUp();

		$this->adapter = new ZipAdapter( WP_Gistpen::$plugin_name, WP_Gistpen::$version );
	}

	function test_build_by_array_complete() {
		$data = array(
			'description' => 'This zip',
			'status'      => 'publish',
			'ID'          => 123,
			'password'    => 'asdf',
			'gist_id'     => '12345',
		);

		$zip = $this->adapter->by_array( $data );

		$this->assertEquals( 'This zip', $zip->get_description() );
		$this->assertEquals( 'publish', $zip->get_status() );
		$this->assertEquals( 123, $zip->get_ID() );
		$this->assertEquals( 'asdf', $zip->get_password() );
		$this->assertEquals( '12345', $zip->get_gist_id() );
	}

	function test_build_by_array_with_extra_vars() {
		$data = array(
			'description' => 'This zip',
			'status'      => 'publish',
			'ID'          => 123,
			'password'    => 'asdf',
			'gist_id'     => '12345',
			'extra'       => 'Something irrelevant',
		);

		$zip = $this->adapter->by_array( $data );

		$this->assertEquals( 'This zip', $zip->get_description() );
		$this->assertEquals( 'publish', $zip->get_status() );
		$this->assertEquals( 123, $zip->get_ID() );
		$this->assertEquals( 'asdf', $zip->get_password() );
		$this->assertEquals( '12345', $zip->get_gist_id() );
	}

	function test_build_by_array_with_only_ID() {
		$data = array(
			'ID'    => 123
		);

		$zip = $this->adapter->by_array( $data );

		$this->assertEquals( '', $zip->get_description() );
		$this->assertEquals( '', $zip->get_status() );
		$this->assertEquals( 123, $zip->get_ID() );
		$this->assertEquals( '', $zip->get_password() );
		$this->assertEquals( 'none', $zip->get_gist_id() );
	}

	function test_build_by_array_with_only_description() {
		$data = array(
			'description' => 'This zip'
		);

		$zip = $this->adapter->by_array( $data );

		$this->assertEquals( 'This zip', $zip->get_description() );
		$this->assertEquals( '', $zip->get_status() );
		$this->assertEquals( null, $zip->get_ID() );
		$this->assertEquals( '', $zip->get_password() );
		$this->assertEquals( 'none', $zip->get_gist_id() );
	}

	function test_build_by_array_with_only_status() {
		$data = array(
			'status' => 'publish'
		);

		$zip = $this->adapter->by_array( $data );

		$this->assertEquals( '', $zip->get_description() );
		$this->assertEquals( 'publish', $zip->get_status() );
		$this->assertEquals( null, $zip->get_ID() );
		$this->assertEquals( '', $zip->get_password() );
		$this->assertEquals( 'none', $zip->get_gist_id() );
	}

	function test_build_by_array_with_only_password() {
		$data = array(
			'password' => 'asdf'
		);

		$zip = $this->adapter->by_array( $data );

		$this->assertEquals( '', $zip->get_description() );
		$this->assertEquals( '', $zip->get_status() );
		$this->assertEquals( null, $zip->get_ID() );
		$this->assertEquals( 'asdf', $zip->get_password() );
		$this->assertEquals( 'none', $zip->get_gist_id() );
	}

	function test_build_by_array_with_only_gist_id() {
		$data = array(
			'gist_id' => '12345'
		);

		$zip = $this->adapter->by_array( $data );

		$this->assertEquals( '', $zip->get_description() );
		$this->assertEquals( '', $zip->get_status() );
		$this->assertEquals( null, $zip->get_ID() );
		$this->assertEquals( '', $zip->get_password() );
		$this->assertEquals( '12345', $zip->get_gist_id() );
	}

	function test_build_by_post() {
		$post = new WP_Post( new stdClass );
		$post->post_title = 'This zip';
		$post->post_status = 'publish';
		$post->post_password = 'asdf';
		$post->ID = 123;
		$post->gist_id = '12345';

		$zip = $this->adapter->by_post( $post );

		$this->assertEquals( 'This zip', $zip->get_description() );
		$this->assertEquals( 'publish', $zip->get_status() );
		$this->assertEquals( 123, $zip->get_ID() );
		$this->assertEquals( 'asdf', $zip->get_password() );
		$this->assertEquals( '12345', $zip->get_gist_id() );
	}

	function tearDown() {
		parent::tearDown();
	}
}
