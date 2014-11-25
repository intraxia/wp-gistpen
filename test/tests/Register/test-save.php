<?php
use WP_Gistpen\Facade\Database;

class WP_Gistpen_Register_Save_Test extends WP_Gistpen_UnitTestCase {

	function setUp() {
		parent::setUp();

		$this->database = new Database( WP_Gistpen::$plugin_name, WP_Gistpen::$version );
	}

	// these stubs could be useful later but aren't necessary now
	function test_failed_without_perms() {
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	function test_failed_if_revision() {
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	function test_failed_if_autosave() {
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	function test_failed_without_ids() {
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
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

		$this->persistance->save_gistpen( $this->gistpen->ID );

		$zip = $this->database->query()->by_id( $this->gistpen->ID );

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

		$this->persistance->save_gistpen( $this->gistpen->ID );

		$zip = Query::get( $this->gistpen->ID );

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
