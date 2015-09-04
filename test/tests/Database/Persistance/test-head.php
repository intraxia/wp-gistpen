<?php

use Intraxia\Gistpen\Database\Persistance\Head as Persistance;
use Intraxia\Gistpen\Database\Query\Head as Query;
use Intraxia\Gistpen\Facade\Adapter;

/**
 * @group  database
 */
class Persistance_Head_Test extends \Intraxia\Gistpen\Test\UnitTestCase {

	function setUp() {
		parent::setUp();

		$this->_setRole( 'administrator' );
		$this->create_post_and_children();

		$this->persistance = new Persistance();
		$this->query = new Query();
		$this->adapter = new Adapter();

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
		$this->assertInternalType( 'array', $result );
		$this->assertInternalType( 'int', $result['zip'] );
		$this->assertInternalType( 'array', $result['files'] );
		$this->assertInternalType( 'array', $result['deleted'] );

		$saved_zip = $this->query->by_id( $result['zip'] );

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
		$this->assertInternalType( 'array', $result );
		$this->assertInternalType( 'int', $result['zip'] );
		$this->assertEmpty( $result['files'] );
		$this->assertEmpty( $result['deleted'] );

		$saved_zip = $this->query->by_id( $result['zip'] );

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

		$this->assertInstanceOf( 'Intraxia\Gistpen\Model\File', $saved_file );
		$this->assertEquals( 'new-code', $saved_file->get_slug() );
		$this->assertEquals( 'if possible do this', $saved_file->get_code() );
		$this->assertEquals( 'twig', $saved_file->get_language()->get_slug() );
	}

	function test_save_gist_id() {
		$this->persistance->set_gist_id( $this->gistpen->ID, '12345' );

		$this->assertEquals( '12345', get_post_meta( $this->gistpen->ID, '_wpgp_gist_id', true ) );
	}

	function tearDown() {
		parent::tearDown();
	}
}
