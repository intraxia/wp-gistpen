<?php

use WP_Gistpen\Database\Query;
use WP_Gistpen\Gistpen\Zip;
use WP_Gistpen\Gistpen\File;
use WP_Gistpen\Gistpen\Language;

/**
 * @group objects
 * @group query
 */
class WP_Gistpen_Query_Test extends WP_Gistpen_UnitTestCase {

	public $query;

	function setUp() {
		parent::setUp();

		$this->create_post_and_children();
	}

	function test_succeeeded_get_all_files() {
		$results = Query::search( null );

		$this->assertInternalType( 'array', $results );
		$this->assertCount( 4, $results );
	}

	function test_failed_create_not_gistpen() {
		$result = Query::create( new WP_Post( new stdClass ) );

		$this->assertInstanceOf( 'WP_Error', $result );
	}

	function test_succeeded_create_post() {
		$post = new stdClass;
		$post->post_type = 'gistpen';
		$result = Query::create( new WP_Post( $post ) );

		$this->assertInstanceOf( 'WP_Gistpen\Gistpen\Zip', $result );
	}

	function test_failed_no_language() {
		$post = new stdClass;
		$post->post_type = 'gistpen';
		$post->post_parent = 5;
		$result = Query::create( new WP_Post( $post ) );

		$this->assertInstanceOf( 'WP_Error', $result );
	}

	function test_succeeded_non_language_to_bash() {
		$post = new stdClass;
		$post->post_type = 'gistpen';
		$post->post_parent = 5;
		$result = Query::create( new WP_Post( $post ), 'unreal_lang' );

		$this->assertInstanceOf( 'WP_Gistpen\Gistpen\File', $result );
		$this->assertEquals( 'bash', $result->language->slug );
	}

	function test_succeeded_create_file() {
		$post = new stdClass;
		$post->post_type = 'gistpen';
		$post->post_parent = 5;
		$result = Query::create( new WP_Post( $post ), 'php' );

		$this->assertInstanceOf( 'WP_Gistpen\Gistpen\File', $result );
	}

	function test_get_language_term_by_post() {
		$result = Query::get_language_term_by_post( get_post( $this->files[0] ) );

		$this->assertInstanceOf( 'stdClass', $result );
	}

	function test_get_language_term_by_slug() {
		foreach ( Language::$supported as $name => $slug ) {
			$result = Query::get_language_term_by_slug( $slug );

			$this->assertInstanceOf( 'stdClass', $result );
			$this->assertEquals( $name, $result->name );
		}
	}

	function test_succeeded_get_post_w_obj() {
		$post = Query::get( $this->gistpen );

		$this->assertInstanceOf( 'WP_Gistpen\Gistpen\Zip', $post );
	}

	function test_succeeded_get_post_w_id() {
		$post = Query::get( $this->gistpen->ID );

		$this->assertInstanceOf( 'WP_Gistpen\Gistpen\Zip', $post );
	}

	function test_succeeded_get_file_w_obj() {
		$file = Query::get( get_post( $this->files[0] ) );

		$this->assertInstanceOf( 'WP_Gistpen\Gistpen\File', $file );
	}

	function test_succeeded_get_file_w_id() {
		$file = Query::get( $this->files[0] );

		$this->assertInstanceOf( 'WP_Gistpen\Gistpen\File', $file );
	}

	function test_failed_get_not_gistpen() {
		$post_id = $this->factory->post->create();
		$post = Query::get( $post_id );

		$this->assertInstanceOf( 'WP_Error', $post );
	}

	function test_failed_save_not_gistpen() {
		$result = Query::save( new WP_Post( new stdClass ) );

		$this->assertInstanceOf( 'WP_Error', $result );
	}

	function test_save_with_post() {
		$post = Query::get( $this->gistpen );

		$post->description = "New description";

		foreach ( $post->files as &$file ) {
			$file->slug = "New code";
			$file->code = "if possible do this";
			$file->language->slug = "twig";
		}

		$result = Query::save( $post );

		// Check result
		$this->assertInternalType( 'int', $result );

		$post = Query::get( $this->gistpen );

		$this->assertEquals( "New description", $post->description );

		foreach ( $post->files as $file ) {
			$this->assertContains( 'new-code', $file->slug );
			$this->assertEquals( 'if possible do this', $file->code );
			$this->assertEquals( 'twig', $file->language->slug );
		}
	}

	function test_succeeded_save_new_file() {
		$file = Query::get( get_post( $this->files[0] ) );

		$file->slug = "New code";
		$file->code = "if possible do this";
		$file->language->slug = "twig";

		$result = Query::save( $file );

		// Check result
		$this->assertInternalType( 'int', $result );

		$file = Query::get( get_post( $this->files[0] ) );

		$this->assertEquals( 'new-code', $file->slug );
		$this->assertEquals( 'if possible do this', $file->code );
		$this->assertEquals( 'twig', $file->language->slug );
	}

	function test_succeeded_save_post_no_files() {
		$post_data = new stdClass;
		$post_data->post_type = 'gistpen';

		$post = Query::create( new WP_Post( $post_data ) );

		$result = Query::save( $post );

		// Check result
		$this->assertInternalType( 'int', $result );

		$this->assertCount( 0, get_children( array( 'post_parent' => $result ) ) );
	}

	function test_succeeded_save_post_with_files() {
		$post = Query::get( $this->gistpen );

		$post->description = "New description";

		foreach ( $post->files as &$file ) {
			$file->slug = "New code";
			$file->code = "if possible do this";
			$file->language->slug = "twig";
		}

		$result = Query::save( $post );

		// Check result
		$this->assertInternalType( 'int', $result );

		$post = Query::get( $result );

		$this->assertEquals( "New description", $post->description );

		foreach ( $post->files as $file ) {
			$this->assertContains( 'new-code', $file->slug );
			$this->assertEquals( 'if possible do this', $file->code );
			$this->assertEquals( 'twig', $file->language->slug );
		}
	}

	function test_succeeded_save_new_post_with_file() {
		$post_data = new WP_Post( new stdClass );
		$post_data->post_type = 'gistpen';
		$post_data->post_status = 'draft';

		$result = Query::create( $post_data );

		$this->assertInstanceOf( 'WP_Gistpen\Gistpen\Zip', $result );
		$this->assertEmpty( $result->files );

		$result->description = "New Description";

		$file = new File( new WP_Post( new stdClass ), new Language( new stdClass  ) );

		$file->slug = 'new-gistpen';
		$file->code = 'echo $stuff';
		$file->language->slug = 'php';

		$result->files[] = $file;

		$result = Query::save( $result );

		$this->assertInternalType( 'integer', $result );
		$this->assertTrue( $result !== 0 );

		$post = get_post( $result );

		$this->assertNotEquals( null, $post );
		$this->assertEquals( 'New Description', $post->post_title );
		$this->assertEquals( 'draft', $post->post_status );
		$this->assertCount( 1, get_children( array(
			'post_type' => 'gistpen',
			'post_parent' => $post->ID,
			'post_status' => $post->post_status,
			'order' => 'ASC',
			'orderby' => 'date',
		) ) );
	}

	function test_succeeded_save_post_with_new_file() {
		$zip = Query::get( $this->gistpen );

		$file = new File( new WP_Post( new stdClass ), new Language( new stdClass  ) );

		$file->slug = 'new-gistpen';
		$file->code = 'echo $stuff';
		$file->language->slug = 'php';

		$zip->files[] = $file;

		$result = Query::save( $zip );

		$this->assertInternalType( 'integer', $result );
		$this->assertTrue( $result !== 0 );

		$post = get_post( $result );

		$this->assertNotEquals( null, $post );
		$this->assertCount( 4, get_children( array(
			'post_type' => 'gistpen',
			'post_parent' => $post->ID,
			'post_status' => $post->post_status,
			'order' => 'ASC',
			'orderby' => 'date',
		) ) );
	}

	function tearDown() {
		parent::tearDown();
	}
}
