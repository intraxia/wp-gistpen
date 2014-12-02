<?php

use WP_Gistpen\Database\Persistance\Head as Persistance;
use WP_Gistpen\Database\Query\Head as Query;
use WP_Gistpen\Facade\Adapter;

/**
 * @group  database
 */
class WP_Gistpen_Persistance_Test extends WP_Gistpen_UnitTestCase {

	function setUp() {
		parent::setUp();

		$this->_setRole( 'administrator' );
		$this->create_post_and_children();

		$this->persistance = new Persistance( WP_Gistpen::$plugin_name, WP_Gistpen::$version );
		$this->query = new Query( WP_Gistpen::$plugin_name, WP_Gistpen::$version );
		$this->adapter = new Adapter( WP_Gistpen::$plugin_name, WP_Gistpen::$version );

		$this->zip = $this->adapter->build( 'zip' )->blank();
		$this->file = $this->adapter->build( 'file' )->blank();
	}

	function test_succeeded_save_by_zip() {
		$this->zip->set_description( "New description" );

		$this->file->set_slug( "New code" );
		$this->file->set_code( "if possible do this" );
		$this->file->set_language( $this->adapter->build( 'language' )->by_slug( 'twig' ) );

		$this->zip->add_file( $this->file );

		$result = $this->persistance->by_zip( $this->zip );

		// Check result
		$this->assertInternalType( 'int', $result );

		$saved_zip = $this->query->by_id( $result );

		$this->assertEquals( "New description", $saved_zip->get_description() );

		$files = $saved_zip->get_files();

		$this->assertCount( 1, $files );

		$file = array_pop( $files );

		$this->assertContains( 'new-code', $file->get_slug() );
		$this->assertEquals( 'if possible do this', $file->get_code() );
		$this->assertEquals( 'twig', $file->get_language()->get_slug() );
	}

	function test_succeeded_save_by_zip_no_files() {
		$this->zip->set_description( "New description" );

		$result = $this->persistance->by_zip( $this->zip );

		// Check result
		$this->assertInternalType( 'int', $result );

		$saved_zip = $this->query->by_id( $result );

		$this->assertEquals( "New description", $saved_zip->get_description() );

		$files = $saved_zip->get_files();

		$this->assertCount( 0, $files );
	}

	function test_succeeded_save_file_by_zip_id() {
		$this->create_post_and_children();

		$this->file->set_slug( "New code" );
		$this->file->set_code( "if possible do this" );
		$this->file->set_language( $this->adapter->build( 'language' )->by_slug( 'twig' ) );

		$result = $this->persistance->by_file_and_zip_id( $this->file, $this->gistpen->ID );

		// Check result
		$this->assertInternalType( 'int', $result );

		$saved_file = $this->query->by_id( $result );

		$this->assertInstanceOf( 'WP_Gistpen\Model\File', $saved_file );
		$this->assertEquals( 'new-code', $saved_file->get_slug() );
		$this->assertEquals( 'if possible do this', $saved_file->get_code() );
		$this->assertEquals( 'twig', $saved_file->get_language()->get_slug() );
	}

	function tearDown() {
		parent::tearDown();
	}
}
