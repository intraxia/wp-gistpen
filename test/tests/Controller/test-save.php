<?php
use WP_Gistpen\Facade\Database;
use WP_Gistpen\Controller\Save;

/**
 * @group controller
 */
class WP_Gistpen_Controller_Save_Test extends WP_Gistpen_UnitTestCase {

	function setUp() {
		parent::setUp();

		$this->database = new Database( WP_Gistpen::$plugin_name, WP_Gistpen::$version );

		$this->save = new Save( WP_Gistpen::$plugin_name, WP_Gistpen::$version );

		$this->create_post_and_children();
		$this->_setRole( 'administrator' );
	}

	function test_update_zip() {
		$zip_data = array(
			'ID'          => $this->gistpen->ID,
			'description' => 'New Description',
			'status'      => 'pending',
			'password'    => '',
			'files'       => array(),
		);

		foreach ( $this->files as $file_id ) {
			$zip_data['files'][] = array(
				'ID'       => $file_id,
				'slug'     => 'new-slug-' . $file_id,
				'code'     => "put {$file_id}",
				'language' => 'ruby'
			);
		}

		$result = $this->save->update( $zip_data );

		$this->assertInternalType( 'int', $result );

		$zip = $this->database->query( 'head' )->by_id( $result );

		$this->assertEquals( 'New Description', $zip->get_description() );
		$this->assertEquals( 'pending', $zip->get_status() );
		$this->assertCount( 3, $zip->get_files() );

		$files = $zip->get_files();

		foreach ( $files as $file ) {
			$file_id = $file->get_ID();
			$this->assertEquals( 'new-slug-' . $file_id, $file->get_slug() );
			$this->assertEquals( "put {$file_id}", $file->get_code() );
			$this->assertEquals( 'ruby', $file->get_language()->get_slug() );
		}
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
			$this->assertEquals( null, get_post( $file_id ) );
		}
	}

	function tearDown() {
		parent::tearDown();
	}
}
