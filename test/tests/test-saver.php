<?php

/**
 * @group  saver
 */
class WP_Gistpen_Saver_Test extends WP_Gistpen_UnitTestCase {

	function setUp() {
		parent::setUp();

		$this->_setRole( 'administrator' );
		$this->create_post_and_children();
	}

	// these stubs could be useful later but aren't necessary now
	function test_failed_without_perms() {

	}

	function test_failed_if_revision() {

	}

	function test_failed_if_autosave() {

	}

	function test_failed_without_ids() {

	}

	function test_succeeded_update_post() {
		$_POST['file_ids'] = '';
		// Set up $_POST variables
		foreach ( $this->files as $file_id ) {
			$_POST['file_ids'] .= ' ' . $file_id;

			$file_id = "-" . $file_id;

			$_POST['wp-gistpenfile-slug' . $file_id] = "New title " . $file_id;
			$_POST['wp-gistpenfile-code' . $file_id] = "New content " . $file_id;
			$_POST['wp-gistpenfile-language' . $file_id] = 'js';
		}

		WP_Gistpen_Saver::save_gistpen( $this->gistpen->ID );

		$zip = WP_Gistpen::get_instance()->query->get( $this->gistpen->ID );

		$this->assertCount( 3, $zip->files );

		foreach ( $zip->files as $file ) {
			$this->assertContains( 'new-title', $file->slug );
			$this->assertContains( 'New content', $file->code );
			$this->assertEquals( 'js', $file->language->slug );
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

		$file_id = "-" . $file_id;

		$_POST['wp-gistpenfile-slug' . $file_id] = "New title " . $file_id;
		$_POST['wp-gistpenfile-code' . $file_id] = "New content " . $file_id;
		$_POST['wp-gistpenfile-language' . $file_id] = 'js';

		WP_Gistpen_Saver::save_gistpen( $this->gistpen->ID );

		$zip = WP_Gistpen::get_instance()->query->get( $this->gistpen->ID );

		$this->assertCount( 4, $zip->files );

		foreach ( $zip->files as $file ) {
			$this->assertContains( 'new-title', $file->slug );
			$this->assertContains( 'New content', $file->code );
			$this->assertEquals( 'js', $file->language->slug );
		}
	}

	function tearDown() {
		parent::tearDown();
	}
}
