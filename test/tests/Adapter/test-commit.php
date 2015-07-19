<?php
use WP_Gistpen\Adapter\Commit as CommitAdapter;

/**
 * @group adapters
 */
class WP_Gistpen_CommitAdapter_Test extends WP_Gistpen_UnitTestCase {

	function setUp() {
		parent::setUp();

		$this->adapter = new CommitAdapter();
		$this->time = current_time( 'mysql' );
	}

	function test_build_by_array_complete() {
		$data = array(
			'description' => 'This zip',
			'status'      => 'publish',
			'ID'          => 123,
			'gist_id'     => '12345',
			'create_date' => $this->time,
		);

		$commit = $this->adapter->by_array( $data );

		$this->assertEquals( 'This zip', $commit->get_description() );
		$this->assertEquals( 'publish', $commit->get_status() );
		$this->assertEquals( 123, $commit->get_ID() );
		$this->assertEquals( '12345', $commit->get_gist_id() );
		$this->assertEquals( $this->time, $commit->get_create_date() );
	}

	function test_build_by_array_with_extra_vars() {
		$data = array(
			'description' => 'This zip',
			'status'      => 'publish',
			'ID'          => 123,
			'gist_id'     => '12345',
			'create_date' => $this->time,
			'extra'       => 'Something irrelevant',
		);

		$commit = $this->adapter->by_array( $data );

		$this->assertEquals( 'This zip', $commit->get_description() );
		$this->assertEquals( 'publish', $commit->get_status() );
		$this->assertEquals( 123, $commit->get_ID() );
		$this->assertEquals( '12345', $commit->get_gist_id() );
		$this->assertEquals( $this->time, $commit->get_create_date() );
	}

	function test_build_by_array_with_only_ID() {
		$data = array(
			'ID'    => 123
		);

		$commit = $this->adapter->by_array( $data );

		$this->assertEquals( '', $commit->get_description() );
		$this->assertEquals( '', $commit->get_status() );
		$this->assertEquals( 123, $commit->get_ID() );
		$this->assertEquals( 'none', $commit->get_gist_id() );
		$this->assertEquals( '', $commit->get_create_date() );
	}

	function test_build_by_array_with_only_description() {
		$data = array(
			'description' => 'This zip'
		);

		$commit = $this->adapter->by_array( $data );

		$this->assertEquals( 'This zip', $commit->get_description() );
		$this->assertEquals( '', $commit->get_status() );
		$this->assertEquals( null, $commit->get_ID() );
		$this->assertEquals( 'none', $commit->get_gist_id() );
		$this->assertEquals( '', $commit->get_create_date() );
	}

	function test_build_by_array_with_only_status() {
		$data = array(
			'status' => 'publish'
		);

		$commit = $this->adapter->by_array( $data );

		$this->assertEquals( '', $commit->get_description() );
		$this->assertEquals( 'publish', $commit->get_status() );
		$this->assertEquals( null, $commit->get_ID() );
		$this->assertEquals( 'none', $commit->get_gist_id() );
		$this->assertEquals( '', $commit->get_create_date() );
	}

	function test_build_by_array_with_only_gist_id() {
		$data = array(
			'gist_id' => '12345'
		);

		$commit = $this->adapter->by_array( $data );

		$this->assertEquals( '', $commit->get_description() );
		$this->assertEquals( '', $commit->get_status() );
		$this->assertEquals( null, $commit->get_ID() );
		$this->assertEquals( '12345', $commit->get_gist_id() );
		$this->assertEquals( '', $commit->get_create_date() );
	}

	function test_build_by_array_with_only_create_date() {
		$data = array(
			'create_date' => $this->time
		);

		$commit = $this->adapter->by_array( $data );

		$this->assertEquals( '', $commit->get_description() );
		$this->assertEquals( '', $commit->get_status() );
		$this->assertEquals( null, $commit->get_ID() );
		$this->assertEquals( 'none', $commit->get_gist_id() );
		$this->assertEquals( $this->time, $commit->get_create_date() );
	}

	function test_build_by_post() {
		$post = new WP_Post( new stdClass );
		$post->post_title = 'This zip';
		$post->post_status = 'publish';
		$post->post_password = 'asdf';
		$post->ID = 123;
		$post->gist_id = '12345';
		$post->post_date_gmt = $this->time;

		$commit = $this->adapter->by_post( $post );

		$this->assertEquals( 'This zip', $commit->get_description() );
		$this->assertEquals( 'publish', $commit->get_status() );
		$this->assertEquals( 123, $commit->get_ID() );
		$this->assertEquals( '12345', $commit->get_gist_id() );
		$this->assertEquals( $this->time, $commit->get_create_date() );
	}

	function tearDown() {
		parent::tearDown();
	}
}
