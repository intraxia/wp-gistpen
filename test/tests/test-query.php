<?php

/**
 * @group objects
 * @group query
 */
class WP_Gistpen_Query_Test extends WP_Gistpen_UnitTestCase {

	public $query;

	function setUp() {
		parent::setUp();
		$this->query = new WP_Gistpen_Query;

		$this->create_post_and_children();
	}

	function test_search_returns_all_files() {
		$results = $this->query->search( null );

		$this->assertInternalType( 'array', $results );
		$this->assertCount( 4, $results );
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

	function test_fail_to_save_non_gistpen() {
		$result = $this->query->save( new WP_Post( new stdClass ) );

		$this->assertInstanceOf( 'WP_Error', $result );
	}

	function test_save_with_post() {
		$post = $this->query->get( $this->gistpen );

		$post->description = "New description";

		foreach ( $post->files as &$file ) {
			$file->slug = "New code";
			$file->code = "if possible do this";
			$file->language->slug = "twig";
		}

		$result = $this->query->save( $post );

		// Check result
		$this->assertInternalType( 'int', $result );

		$post = $this->query->get( $this->gistpen );

		$this->assertEquals( "New description", $post->description );

		foreach ( $post->files as $file ) {
			$this->assertContains( 'new-code', $file->slug );
			$this->assertEquals( 'if possible do this', $file->code );
			$this->assertEquals( 'twig', $file->language->slug );
		}
	}

	function test_save_with_file() {
		$file = $this->query->get( get_post( $this->files[0] ) );

		$file->slug = "New code";
		$file->code = "if possible do this";
		$file->language->slug = "twig";

		$result = $this->query->save( $file );

		// Check result
		$this->assertInternalType( 'int', $result );

		$file = $this->query->get( get_post( $this->files[0] ) );

		$this->assertEquals( 'new-code', $file->slug );
		$this->assertEquals( 'if possible do this', $file->code );
		$this->assertEquals( 'twig', $file->language->slug );
	}

	function test_save_post_with_no_files() {
		$post_data = new stdClass;
		$post_data->post_type = 'gistpen';

		$post = $this->query->create( new WP_Post( $post_data ) );

		$result = $this->query->save( $post );

		// Check result
		$this->assertInternalType( 'int', $result );

		$this->assertCount( 1, get_children( array( 'post_parent' => $result ) ) );
	}

	function test_save_post() {
		$post = $this->query->get( $this->gistpen );

		$post->description = "New description";

		foreach ( $post->files as &$file ) {
			$file->slug = "New code";
			$file->code = "if possible do this";
			$file->language->slug = "twig";
		}

		$post->update_post();

		$result = $this->query->save_post( $post );

		// Check result
		$this->assertInternalType( 'int', $result );

		$post = $this->query->get( $this->gistpen );

		$this->assertEquals( "New description", $post->description );

		foreach ( $post->files as $file ) {
			$this->assertContains( 'new-code', $file->slug );
			$this->assertEquals( 'if possible do this', $file->code );
			$this->assertEquals( 'twig', $file->language->slug );
		}
	}

	function test_save_file() {
		$file = $this->query->get( get_post( $this->files[0] ) );

		$file->slug = "New code";
		$file->code = "if possible do this";
		$file->language->slug = "twig";

		$file->update_post();

		$result = $this->query->save_file( $file );

		// Check result
		$this->assertInternalType( 'int', $result );

		$file = $this->query->get( get_post( $this->files[0] ) );

		$this->assertContains( 'new-code', $file->slug );
		$this->assertEquals( 'if possible do this', $file->code );
		$this->assertEquals( 'twig', $file->language->slug );
	}

	function test_save_new_post() {
		$post_data = new WP_Post( new stdClass );
		$post_data->post_type = 'gistpen';
		$post_data->post_status = 'draft';

		$result = WP_Gistpen::get_instance()->query->create( $post_data );

		$this->assertInstanceOf( 'WP_Gistpen_Post', $result );

		$result->description = "New Description";

		$result->files[0]->slug = 'new-gistpen';
		$result->files[0]->code = 'echo $stuff';
		$result->files[0]->language->slug = 'php';

		$result = WP_Gistpen::get_instance()->query->save( $result );

		$this->assertInternalType( 'integer', $result );
		$this->assertTrue( $result !== 0 );
		$this->assertNotEquals( null, get_post( $result ) );
		$this->assertEquals( 'New Description', get_post( $result )->post_title );
		$this->assertEquals( 'draft', get_post( $result )->post_status );
		$this->assertCount( 1, get_children( array( 'post_parent' => $result ) ) );
	}

	function tearDown() {
		parent::tearDown();
	}
}
