<?php
use WP_Gistpen\Facade\Database;
use WP_Gistpen\Register\Save;

/**
 * @group register
 */
class WP_Gistpen_Register_Save_Test extends WP_Gistpen_UnitTestCase {

	function setUp() {
		parent::setUp();

		$this->database = new Database( WP_Gistpen::$plugin_name, WP_Gistpen::$version );

		$this->save = new Save( WP_Gistpen::$plugin_name, WP_Gistpen::$version );

		$this->create_post_and_children();
		$this->_setRole( 'administrator' );
	}

	function test_save_revision() {
		wp_save_post_revision( $this->gistpen->ID );
	}

	function test_succeeded_update_post() {
		$_POST['file_ids'] = '';
		// Set up $_POST variables
		foreach ( $this->files as $file_id ) {
			$_POST['file_ids'] .= ' ' . $file_id;

			$_POST['wp-gistpenfile-slug-' . $file_id] = "New title " . $file_id;
			$_POST['wp-gistpenfile-code-' . $file_id] = "New content " . $file_id;
			$_POST['wp-gistpenfile-language-' . $file_id] = 'js';
		}

		wp_update_post( array( 'ID'=> $this->gistpen->ID, 'post_type' => 'gistpen' ) );

		$zip = $this->database->query()->by_id( $this->gistpen->ID );

		$files = $zip->get_files();

		$this->assertCount( 3, $files );

		foreach ( $files as $file ) {
			$this->assertEquals( 'new-title-' . $file->get_ID(), $file->get_slug() );
			$this->assertEquals( 'New content ' . $file->get_ID(), $file->get_code() );
			$this->assertEquals( 'js', $file->get_language()->get_slug() );
		}

		$revisions = wp_get_post_revisions( $this->gistpen->ID );

		$this->assertCount( 1, $revisions );

		$revision = array_pop( $revisions );
		$revision_id = $revision->ID;

		$revision_meta = get_post_meta( $this->gistpen->ID, 'wpgp_revisions', true );

		$this->assertCount( 3, $revision_meta[ $revision_id ]['files'] );

		foreach ( $revision_meta[ $revision_id ]['files'] as $file_id ) {
			$this->assertEquals( 'revision', get_post_type( $file_id ) );
		}
	}

	function test_succeeded_save_with_new_file() {
		$_POST['file_ids'] = '';
		// Set up $_POST variables
		foreach ( $this->files as $file_id ) {
			$_POST['file_ids'] .= ' ' . $file_id;

			$file_id = "-" . $file_id;

			$_POST['wp-gistpenfile-slug' . $file_id] = "New title " . $file_id;
			$_POST['wp-gistpenfile-code' . $file_id] = "New content " . $file_id;
			$_POST['wp-gistpenfile-language' . $file_id] = 'js';
		}

		// And an extra
		$file_id = '12345';

		$_POST['file_ids'] .= ' ' . $file_id;

		$file_id_w_dash = "-" . $file_id;

		$_POST['wp-gistpenfile-slug' . $file_id_w_dash] = "New title " . $file_id;
		$_POST['wp-gistpenfile-code' . $file_id_w_dash] = "New content " . $file_id;
		$_POST['wp-gistpenfile-language' . $file_id_w_dash] = 'js';

		wp_update_post( array( 'ID'=> $this->gistpen->ID ) );

		$zip = $this->database->query()->by_id( $this->gistpen->ID );

		$files = $zip->get_files();

		$this->assertCount( 4, $files );

		foreach ( $files as $file ) {
			$this->assertContains( 'new-title', $file->get_slug() );
			$this->assertContains( 'New content', $file->get_code() );
			$this->assertEquals( 'js', $file->get_language()->get_slug() );
		}

		$revisions = wp_get_post_revisions( $this->gistpen->ID );

		$this->assertCount( 1, $revisions );

		$revision = array_pop( $revisions );
		$revision_id = $revision->ID;

		$revision_meta = get_post_meta( $this->gistpen->ID, 'wpgp_revisions', true );

		$this->assertCount( 4, $revision_meta[ $revision_id ]['files'] );

		foreach ( $revision_meta[ $revision_id ]['files'] as $file_id ) {
			$this->assertEquals( 'revision', get_post_type( $file_id ) );
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
			$this->assertEquals( null, get_post($file_id) );
		}
	}

	function tearDown() {
		parent::tearDown();
	}
}
