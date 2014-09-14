<?php

/**
 * @group objects
 */
class WP_Gistpen_Query_Test extends WP_Gistpen_UnitTestCase {

	public $query;

	function setUp() {
		parent::setUp();
		$this->query = new WP_Gistpen_Query;

		$this->create_post_and_children();
	}

	function test_fail_to_create_non_gistpen() {
		$result = $this->query->create( new WP_Post( new stdClass ) );

		$this->assertInstanceOf( 'WP_Error', $result );
	}

	function test_create_post() {
		$post = new stdClass;
		$post->post_type = 'gistpen';
		$result = $this->query->create( new WP_Post( $post ) );

		$this->assertInstanceOf( 'WP_Gistpen_Post', $result );
	}

	function test_needs_language() {
		$post = new stdClass;
		$post->post_type = 'gistpen';
		$post->post_parent = 5;
		$result = $this->query->create( new WP_Post( $post ) );

		$this->assertInstanceOf( 'WP_Error', $result );
	}

	function test_needs_real_language() {
		$post = new stdClass;
		$post->post_type = 'gistpen';
		$post->post_parent = 5;
		$result = $this->query->create( new WP_Post( $post ), 'unreal_lang' );

		$this->assertInstanceOf( 'WP_Error', $result );
	}

	function test_returns_file() {
		$post = new stdClass;
		$post->post_type = 'gistpen';
		$post->post_parent = 5;
		$result = $this->query->create( new WP_Post( $post ), 'php' );

		$this->assertInstanceOf( 'WP_Gistpen_File', $result );
	}

	function test_get_file() {
		$result = $this->query->get_file( get_post( $this->files[0] ) );

		$this->assertInstanceOf( 'WP_Gistpen_File', $result );
	}

	function test_get_language_term_by_post() {
		$result = $this->query->get_language_term_by_post( get_post( $this->files[0] ) );

		$this->assertInstanceOf( 'stdClass', $result );
	}

	function test_get_language_term_by_slug() {
		foreach ( WP_Gistpen_Language::$supported as $name => $slug ) {
			$result = $this->query->get_language_term_by_slug( $slug );

			$this->assertInstanceOf( 'stdClass', $result );
			$this->assertEquals( $name, $result->name );
		}
	}

	function test_get_gistpen() {
		$result = $this->query->get_gistpen( $this->gistpen );

		$this->assertInstanceOf( 'WP_Gistpen_Post', $result );
		$this->assertCount( 3, $result->files );
	}

	function test_get_files() {
		$result = $this->query->get_files( $this->gistpen );

		$this->assertCount( 3, $result );
		foreach ( $result as $file ) {
			$this->assertInstanceOf( 'WP_Gistpen_File', $file );
		}
	}

	function test_get_post_from_obj() {
		$post = $this->query->get( $this->gistpen );

		$this->assertInstanceOf( 'WP_Gistpen_Post', $post );
	}

	function test_get_post_from_id() {
		$post = $this->query->get( $this->gistpen->ID );

		$this->assertInstanceOf( 'WP_Gistpen_Post', $post );
	}

	function test_get_file_from_obj() {
		$file = $this->query->get( get_post( $this->files[0] ) );

		$this->assertInstanceOf( 'WP_Gistpen_File', $file );
	}

	function test_get_file_from_id() {
		$file = $this->query->get( $this->files[0] );

		$this->assertInstanceOf( 'WP_Gistpen_File', $file );
	}

	function tearDown() {
		parent::tearDown();
	}
}
