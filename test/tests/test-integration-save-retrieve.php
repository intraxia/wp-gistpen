<?php

use WP_Gistpen\Controller\Save as SaveController;
use WP_Gistpen\Facade\Database;

/**
 * @group SaveRetrieve
 */
class WP_Gistpen_SaveRetrieve_Test extends WP_Gistpen_UnitTestCase {

	function setUp() {
		parent::setUp();

		$this->save = new SaveController( WP_Gistpen::$plugin_name, WP_Gistpen::$version );
		$this->database = new Database( WP_Gistpen::$plugin_name, WP_Gistpen::$version );

		$this->_setRole( 'administrator' );
		$this->create_post_and_children();
		$this->zip = $this->database->query( 'head' )->by_id( $this->gistpen->ID );
	}

	function test_save_and_retrieve_succeed() {
		$this->add_and_test_first_save();
		$this->add_and_test_second_save();
		$this->add_and_test_third_save();
	}

	function add_and_test_first_save() {
		$zip_data = array(
			'ID'          => $this->zip->get_ID(),
			'description' => 'First Description',
			'status'      => 'pending',
			'password'    => '',
			'files'       => array(),
		);

		foreach ( $this->zip->get_files() as $file ) {
			$zip_data['files'][] = array(
				'ID'       => $file->get_ID(),
				'slug'     => 'first-slug-' . $file->get_ID(),
				'code'     => "put {$file->get_ID()}",
				'language' => 'ruby'
			);
		}

		// @todo pull this back to Ajax API level
		$result = $this->save->update( $zip_data );

		$this->assertInternalType( 'int', $result );

		$this->zip = $this->database->query( 'head' )->by_id( $result );

		$this->assertEquals( 'First Description', $this->zip->get_description() );
		$this->assertEquals( 'pending', $this->zip->get_status() );
		$this->assertCount( 3, $this->zip->get_files() );

		$files = $this->zip->get_files();

		foreach ( $files as $file ) {
			$file_id = (string) $file->get_ID();

			$this->assertStringStartsWith( 'first-slug-', $file->get_slug() );
			$this->assertStringStartsWith( 'put', $file->get_code() );
			$this->assertEquals( 'ruby', $file->get_language()->get_slug() );
		}

		$commit = $this->database->query( 'commit' )->latest_by_head_id( $this->zip->get_ID() );

		$this->assertEquals( 'First Description', $commit->get_description() );
		$this->assertCount( 3, $commit->get_states() );

		$states = $commit->get_states();

		foreach ( $states as $state ) {
			$state_id = $state->get_ID();
			$head_id = $state->get_head_id();

			$this->assertStringStartsWith( 'first-slug-', $state->get_slug() );
			$this->assertStringStartsWith( 'put', $state->get_code() );
			$this->assertEquals( 'ruby', $state->get_language()->get_slug() );
		}

		$history = $this->database->query( 'commit' )->history_by_head_id( $this->zip->get_ID() );

		$this->assertCount( 1, $history );
	}

	function add_and_test_second_save() {
		$zip_data = array(
			'ID'          => $this->zip->get_ID(),
			'description' => 'Second Description',
			'status'      => 'private',
			'password'    => '',
			'files'       => array(),
		);

		foreach ( $this->zip->get_files() as $file ) {
			$zip_data['files'][] = array(
				'ID'       => $file->get_ID(),
				'slug'     => 'second-slug-' . $file->get_ID(),
				'code'     => "echo {$file->get_ID()};",
				'language' => 'php'
			);
		}

		// new file
		$zip_data['files'][] = array(
			'slug'     => 'second-slug-new',
			'code'     => 'echo $new_file;',
			'language' => 'php'
		);

		// @todo pull this back to Ajax API level
		$result = $this->save->update( $zip_data );

		$this->assertInternalType( 'int', $result );

		$this->zip = $this->database->query( 'head' )->by_id( $result );

		$this->assertEquals( 'Second Description', $this->zip->get_description() );
		$this->assertEquals( 'private', $this->zip->get_status() );

		$files = $this->zip->get_files();

		$this->assertCount( 4, $files );

		foreach ( $files as $file ) {
			$file_id = $file->get_ID();
			$this->assertStringStartsWith( 'second-slug-', $file->get_slug() );
			$this->assertStringStartsWith( 'echo', $file->get_code() );
			$this->assertEquals( 'php', $file->get_language()->get_slug() );
		}

		$commit = $this->database->query( 'commit' )->latest_by_head_id( $this->zip->get_ID() );

		$this->assertEquals( 'Second Description', $commit->get_description() );
		$this->assertCount( 4, $commit->get_states() );

		$states = $commit->get_states();

		foreach ( $states as $state ) {
			$state_id = $state->get_ID();
			$head_id = $state->get_head_id();

			$this->assertStringStartsWith( 'second-slug-', $state->get_slug() );
			$this->assertStringStartsWith( 'echo', $state->get_code() );
			$this->assertEquals( 'php', $state->get_language()->get_slug() );
		}

		$history = $this->database->query( 'commit' )->history_by_head_id( $this->zip->get_ID() );

		$this->assertCount( 2, $history );
	}

	function add_and_test_third_save() {
		$zip_data = array(
			'ID'          => $this->zip->get_ID(),
			'description' => 'Third Description',
			'status'      => 'draft',
			'password'    => '',
			'files'       => array(),
		);

		foreach ( $this->zip->get_files() as $file ) {
			$zip_data['files'][] = array(
				'ID'       => $file->get_ID(),
				'slug'     => 'third-slug-' . $file->get_ID(),
				'code'     => "console.log({$file->get_ID()});",
				'language' => 'js'
			);
		}

		// remove the first file
		array_shift( $zip_data['files'] );

		// @todo pull this back to Ajax API level
		$result = $this->save->update( $zip_data );

		$this->assertInternalType( 'int', $result );

		$this->zip = $this->database->query( 'head' )->by_id( $result );

		$this->assertEquals( 'Third Description', $this->zip->get_description() );
		$this->assertEquals( 'draft', $this->zip->get_status() );

		$files = $this->zip->get_files();

		$this->assertCount( 3, $files );

		foreach ( $files as $file ) {
			$file_id = $file->get_ID();
			$this->assertStringStartsWith( 'third-slug-', $file->get_slug() );
			$this->assertStringStartsWith( 'console.log(', $file->get_code() );
			$this->assertEquals( 'js', $file->get_language()->get_slug() );
		}

		$commit = $this->database->query( 'commit' )->latest_by_head_id( $this->zip->get_ID() );

		$this->assertEquals( 'Third Description', $commit->get_description() );
		$this->assertCount( 4, $commit->get_states() );

		$states = $commit->get_states();

		foreach ( $states as $state ) {
			$state_id = $state->get_ID();
			$head_id = $state->get_head_id();

			if ( 'deleted' !== $state->get_status() ) {
				$this->assertStringStartsWith( 'third-slug-', $state->get_slug() );
				$this->assertStringStartsWith( 'console.log(', $state->get_code() );
				$this->assertEquals( 'js', $state->get_language()->get_slug() );
			} else {
				$this->assertStringStartsWith( 'second-slug-', $state->get_slug() );
				$this->assertStringStartsWith( 'echo', $state->get_code() );
				$this->assertEquals( 'php', $state->get_language()->get_slug() );
			}
		}

		$history = $this->database->query( 'commit' )->history_by_head_id( $this->zip->get_ID() );

		$this->assertCount( 3, $history );
	}

	function tearDown() {
		parent::tearDown();
	}
}
