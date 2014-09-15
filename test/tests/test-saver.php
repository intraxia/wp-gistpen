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

	function test_save_normal_post() {
		$_POST['file_ids'] = '';
		// Set up $_POST variables
		foreach ( $this->files as $file_id ) {
			$_POST['file_ids'] .= ' ' . $file_id;

			$file_id = "-" . $file_id;

			$_POST['wp-gistpenfile-slug' . $file_id] = "New title " . $file_id;
			$_POST['wp-gistpenfile-code' . $file_id] = "New content " . $file_id;
			$_POST['post_status'] = 'publish';
			$_POST['wp-gistpenfile-language' . $file_id] = 'js';
		}

		WP_Gistpen_Saver::save_gistpen( $this->gistpen->ID );

		$post = WP_Gistpen::get_instance()->query->get( $this->gistpen->ID );

		foreach ( $post->files as $file ) {
			$this->assertContains( 'new-title', $file->slug );
			$this->assertContains( 'New content', $file->code );
			$this->assertEquals( 'publish', $file->file->post_status );
			$this->assertEquals( 'js', $file->language->slug );
		}
	}

	function test_save_revision() {

	}

	function tearDown() {
		parent::tearDown();
	}
}
