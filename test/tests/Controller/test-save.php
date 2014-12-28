<?php
use WP_Gistpen\Facade\Database;
use WP_Gistpen\Controller\Save;

/**
 * @group register
 */
class WP_Gistpen_Controller_Save_Test extends WP_Gistpen_UnitTestCase {

	function setUp() {
		parent::setUp();

		$this->database = new Database( WP_Gistpen::$plugin_name, WP_Gistpen::$version );

		$this->save = new Save( WP_Gistpen::$plugin_name, WP_Gistpen::$version );

		$this->create_post_and_children();
		$this->_setRole( 'administrator' );
	}

	function test_status_stays_in_sync() {
		$zip = $this->database->query()->by_id( $this->gistpen->ID );

		wp_update_post( array(
			'ID' => $this->gistpen->ID,
			'post_status' => 'pending',
		) );

		$this->assertEquals( 'pending', get_post_status( $zip->get_ID() ) );

		$files = $zip->get_files();

		foreach ( $files as $file ) {
			$this->assertEquals( 'pending', get_post_status( $file->get_ID() ) );
		}

		wp_update_post( array(
			'ID' => $this->gistpen->ID,
			'post_status' => 'trash',
		) );

		$this->assertEquals( 'trash', get_post_status( $zip->get_ID() ) );

		$files = $zip->get_files();

		foreach ( $files as $file ) {
			$this->assertEquals( 'trash', get_post_status( $file->get_ID() ) );
		}
	}

	function test_delete_children() {
		wp_delete_post( $this->gistpen->ID, true );

		foreach ($this->files as $file_id => $value) {
			$this->assertEquals( null, get_post($file_id) );
		}
	}

	function tearDown() {
		parent::tearDown();
	}
}
