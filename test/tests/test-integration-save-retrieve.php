<?php

use Intraxia\Gistpen\Account\Gist;
use Intraxia\Gistpen\Controller\Save as SaveController;
use Intraxia\Gistpen\Facade\Database;
use Intraxia\Gistpen\App;

/**
 * @group integration
 */
class Integration_Test extends \Intraxia\Gistpen\Test\UnitTestCase {

	function setUp() {
		parent::setUp();

		$this->save = new SaveController();
		$this->database = new Database();
	}

	function test_sync_save_and_retrieve() {
		$this->_setRole( 'administrator' );
		$_POST['nonce'] = wp_create_nonce( '_ajax_wp_gistpen' );

		$_POST['zip'] = get_object_vars( json_decode( '{"ID":null,"description":"Twig Example","status":"publish","password":"","gist_id":"da7446048f207c4525e5","sync":"on","files":[{"slug":"twig-file","code":"{% if posts|length %}\n  {% for article in articles %}\n  <div>\n  {{ article.title|upper() }}\n\n  {# outputs \'WELCOME\' #}\n  </div>\n  {% endfor %}\n{% endif %}","ID":null,"language":"twig"}]}' ) );
		foreach ($_POST['zip']['files'] as $id => $file) {
			$_POST['zip']['files'][ $id ] = get_object_vars( $file );
		}

		try {
			$this->_handleAjax( 'save_gistpen' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);
		$this->_last_response = "";

		$string = explode( " ", $this->response->data->message );
		$post_id = array_pop( $string );
		$post_id = intval( $post_id );

		$_POST['post_id'] = $post_id;
		unset( $_POST['zip'] );

		try {
			$this->_handleAjax( 'get_gistpen' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->assertEquals( 'on', $this->response->data->sync );

		$_POST['zip'] = get_object_vars( json_decode( '{"ID":null,"description":"Twig Example","status":"publish","password":"","gist_id":"da7446048f207c4525e5","sync":"off","files":[{"slug":"twig-file","code":"{% if posts|length %}\n  {% for article in articles %}\n  <div>\n  {{ article.title|upper() }}\n\n  {# outputs \'WELCOME\' #}\n  </div>\n  {% endfor %}\n{% endif %}","ID":null,"language":"twig"}]}' ) );
		foreach ($_POST['zip']['files'] as $id => $file) {
			$_POST['zip']['files'][ $id ] = get_object_vars( $file );
		}
		$_POST['zip']['ID'] = $post_id;

		try {
			$this->_handleAjax( 'save_gistpen' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);
		$this->_last_response = "";

		$_POST['post_id'] = $post_id;
		unset( $_POST['zip'] );

		try {
			$this->_handleAjax( 'get_gistpen' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->assertEquals( 'off', $this->response->data->sync );
	}

	function test_save_and_retrieve_succeed() {
		$this->_setRole( 'administrator' );
		$this->create_post_and_children();
		$this->zip = $this->database->query( 'head' )->by_id( $this->gistpen->ID );

		$gist = new GistTest();
		$gist->set_client( $this->mock_github_client );

		$app = App::get();
		$app['Controller\Sync']->gist = $gist;
		cmb2_update_option( \Gistpen::$plugin_name, '_wpgp_gist_token', '1234' );
		$this->mock_github_client
			->shouldReceive( 'authenticate' )
			->times( 3 )
			->shouldReceive( 'create' )
			->once()
			->andReturn( array(
				'id' => 'abcde1234',
				'history' => array(
					array(
						'version' => 'thisversion'
					)
				)
			) )
			->shouldReceive( 'update' )
			->twice()
			->andReturn( array(
				'id' => 'abcde1234',
				'history' => array(
					array(
						'version' => 'thisversion'
					)
				)
			) );

		$this->add_and_test_first_save();
		$this->add_and_test_first_save_export();
		$this->add_and_test_second_save();
		$this->add_and_test_second_save_export();
		$this->add_and_test_third_save();
		$this->add_and_test_third_save_export();
	}

	function add_and_test_first_save() {
		$zip_data = array(
			'ID'          => $this->zip->get_ID(),
			'description' => 'First Description',
			'status'      => 'pending',
			'password'    => '',
			'files'       => array(),
			'gist_id'     => $this->zip->get_gist_id(),
			'sync'        => 'off',
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

		$files = $this->zip->get_files();
		$this->assertCount( 3, $this->zip->get_files() );

		$this->assertEquals( 'none', $this->zip->get_gist_id() );
		$this->assertEquals( 'off', $this->zip->get_sync() );

		foreach ( $files as $file ) {
			$file_id = (string) $file->get_ID();

			$this->assertStringStartsWith( 'first-slug-', $file->get_slug() );
			$this->assertStringStartsWith( 'put', $file->get_code() );
			$this->assertEquals( 'ruby', $file->get_language()->get_slug() );
		}

		$history = $this->database->query( 'commit' )->history_by_head_id( $this->zip->get_ID() );

		$this->assertCount( 1, $history );

		$commit = $this->database->query( 'commit' )->latest_by_head_id( $this->zip->get_ID() );

		$this->assertEquals( 'First Description', $commit->get_description() );
		$this->assertCount( 3, $commit->get_states() );
		$this->assertEquals( 'none', $commit->get_head_gist_id() );
		$this->assertEquals( 'off', $commit->get_sync() );

		$states = $commit->get_states();

		foreach ( $states as $state ) {
			$state_id = $state->get_ID();
			$head_id = $state->get_head_id();

			$this->assertStringStartsWith( 'first-slug-', $state->get_slug() );
			$this->assertStringStartsWith( 'put', $state->get_code() );
			$this->assertEquals( 'ruby', $state->get_language()->get_slug() );
		}
	}

	function add_and_test_first_save_export() {
		$zip_data = array(
			'ID'          => $this->zip->get_ID(),
			'description' => 'First Description',
			'status'      => 'pending',
			'password'    => '',
			'files'       => array(),
			'gist_id'     => $this->zip->get_gist_id(),
			'sync'        => 'on',
		);

		foreach ( $this->zip->get_files() as $file ) {
			$zip_data['files'][] = array(
				'ID'       => $file->get_ID(),
				'slug'     => 'first-slug-' . $file->get_ID(),
				'code'     => "put {$file->get_ID()} 2",
				'language' => 'ruby'
			);
		}

		// @todo pull this back to Ajax API level
		$result = $this->save->update( $zip_data );

		$this->assertInternalType( 'int', $result );

		$this->zip = $this->database->query( 'head' )->by_id( $result );

		$this->assertEquals( 'First Description', $this->zip->get_description() );
		$this->assertEquals( 'pending', $this->zip->get_status() );

		$files = $this->zip->get_files();
		$this->assertCount( 3, $this->zip->get_files() );

		$this->assertEquals( 'abcde1234', $this->zip->get_gist_id() );
		$this->assertEquals( 'on', $this->zip->get_sync() );

		foreach ( $files as $file ) {
			$file_id = (string) $file->get_ID();

			$this->assertStringStartsWith( 'first-slug-', $file->get_slug() );
			$this->assertStringStartsWith( 'put', $file->get_code() );
			$this->assertEquals( 'ruby', $file->get_language()->get_slug() );
		}

		$history = $this->database->query( 'commit' )->history_by_head_id( $this->zip->get_ID() );

		$this->assertCount( 2, $history );

		$commit = $this->database->query( 'commit' )->latest_by_head_id( $this->zip->get_ID() );

		$this->assertEquals( 'First Description', $commit->get_description() );
		$this->assertCount( 3, $commit->get_states() );
		$this->assertEquals( 'abcde1234', $commit->get_head_gist_id() );
		$this->assertEquals( 'on', $commit->get_sync() );

		$states = $commit->get_states();

		foreach ( $states as $state ) {
			$state_id = $state->get_ID();
			$head_id = $state->get_head_id();

			$this->assertStringStartsWith( 'first-slug-', $state->get_slug() );
			$this->assertStringStartsWith( 'put', $state->get_code() );
			$this->assertEquals( 'ruby', $state->get_language()->get_slug() );
		}
	}

	function add_and_test_second_save() {
		$zip_data = array(
			'ID'          => $this->zip->get_ID(),
			'description' => 'Second Description',
			'status'      => 'private',
			'password'    => '',
			'files'       => array(),
			'gist_id'     => $this->zip->get_gist_id(),
			'sync'        => 'off',
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

		$this->assertEquals( 'abcde1234', $this->zip->get_gist_id() );
		$this->assertEquals( 'off', $this->zip->get_sync() );

		foreach ( $files as $file ) {
			$file_id = $file->get_ID();
			$this->assertStringStartsWith( 'second-slug-', $file->get_slug() );
			$this->assertStringStartsWith( 'echo', $file->get_code() );
			$this->assertEquals( 'php', $file->get_language()->get_slug() );
		}

		$history = $this->database->query( 'commit' )->history_by_head_id( $this->zip->get_ID() );

		$this->assertCount( 3, $history );

		$commit = $this->database->query( 'commit' )->latest_by_head_id( $this->zip->get_ID() );

		$this->assertEquals( 'Second Description', $commit->get_description() );
		$this->assertCount( 4, $commit->get_states() );
		$this->assertEquals( 'abcde1234', $commit->get_head_gist_id() );
		$this->assertEquals( 'off', $commit->get_sync() );

		$states = $commit->get_states();

		foreach ( $states as $state ) {
			$state_id = $state->get_ID();
			$head_id = $state->get_head_id();

			$this->assertStringStartsWith( 'second-slug-', $state->get_slug() );
			$this->assertStringStartsWith( 'echo', $state->get_code() );
			$this->assertEquals( 'php', $state->get_language()->get_slug() );
		}
	}

	function add_and_test_second_save_export() {
		$zip_data = array(
			'ID'          => $this->zip->get_ID(),
			'description' => 'Second Description',
			'status'      => 'private',
			'password'    => '',
			'files'       => array(),
			'gist_id'     => $this->zip->get_gist_id(),
			'sync'        => 'on',
		);

		foreach ( $this->zip->get_files() as $file ) {
			$zip_data['files'][] = array(
				'ID'       => $file->get_ID(),
				'slug'     => 'second-slug-' . $file->get_ID(),
				'code'     => "echo {$file->get_ID()} 2;",
				'language' => 'php'
			);
		}

		// @todo pull this back to Ajax API level
		$result = $this->save->update( $zip_data );

		$this->assertInternalType( 'int', $result );

		$this->zip = $this->database->query( 'head' )->by_id( $result );

		$this->assertEquals( 'Second Description', $this->zip->get_description() );
		$this->assertEquals( 'private', $this->zip->get_status() );

		$files = $this->zip->get_files();
		$this->assertCount( 4, $files );

		$this->assertEquals( 'abcde1234', $this->zip->get_gist_id() );
		$this->assertEquals( 'on', $this->zip->get_sync() );

		foreach ( $files as $file ) {
			$file_id = $file->get_ID();
			$this->assertStringStartsWith( 'second-slug-', $file->get_slug() );
			$this->assertStringStartsWith( 'echo', $file->get_code() );
			$this->assertEquals( 'php', $file->get_language()->get_slug() );
		}

		$history = $this->database->query( 'commit' )->history_by_head_id( $this->zip->get_ID() );

		$this->assertCount( 4, $history );

		$commit = $this->database->query( 'commit' )->latest_by_head_id( $this->zip->get_ID() );

		$this->assertEquals( 'Second Description', $commit->get_description() );
		$this->assertCount( 4, $commit->get_states() );
		$this->assertEquals( 'abcde1234', $commit->get_head_gist_id() );
		$this->assertEquals( 'on', $commit->get_sync() );

		$states = $commit->get_states();

		foreach ( $states as $state ) {
			$state_id = $state->get_ID();
			$head_id = $state->get_head_id();

			$this->assertStringStartsWith( 'second-slug-', $state->get_slug() );
			$this->assertStringStartsWith( 'echo', $state->get_code() );
			$this->assertEquals( 'php', $state->get_language()->get_slug() );
		}
	}

	function add_and_test_third_save() {
		$zip_data = array(
			'ID'          => $this->zip->get_ID(),
			'description' => 'Third Description',
			'status'      => 'draft',
			'password'    => '',
			'files'       => array(),
			'gist_id'     => $this->zip->get_gist_id(),
			'sync'        => 'off',
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

		$this->assertEquals( 'abcde1234', $this->zip->get_gist_id() );
		$this->assertEquals( 'off', $this->zip->get_sync() );

		foreach ( $files as $file ) {
			$file_id = $file->get_ID();
			$this->assertStringStartsWith( 'third-slug-', $file->get_slug() );
			$this->assertStringStartsWith( 'console.log(', $file->get_code() );
			$this->assertEquals( 'js', $file->get_language()->get_slug() );
		}

		$history = $this->database->query( 'commit' )->history_by_head_id( $this->zip->get_ID() );

		$this->assertCount( 5, $history );

		$commit = $this->database->query( 'commit' )->latest_by_head_id( $this->zip->get_ID() );

		$this->assertEquals( 'Third Description', $commit->get_description() );
		$this->assertCount( 4, $commit->get_states() );
		$this->assertEquals( 'abcde1234', $commit->get_head_gist_id() );
		$this->assertEquals( 'off', $commit->get_sync() );

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
	}

	function add_and_test_third_save_export() {
		$zip_data = array(
			'ID'          => $this->zip->get_ID(),
			'description' => 'Third Description',
			'status'      => 'draft',
			'password'    => '',
			'files'       => array(),
			'gist_id'     => $this->zip->get_gist_id(),
			'sync'        => 'on',
		);

		foreach ( $this->zip->get_files() as $file ) {
			$zip_data['files'][] = array(
				'ID'       => $file->get_ID(),
				'slug'     => 'third-slug-' . $file->get_ID(),
				'code'     => "console.log({$file->get_ID()} 2);",
				'language' => 'js'
			);
		}

		// @todo pull this back to Ajax API level
		$result = $this->save->update( $zip_data );

		$this->assertInternalType( 'int', $result );

		$this->zip = $this->database->query( 'head' )->by_id( $result );

		$this->assertEquals( 'Third Description', $this->zip->get_description() );
		$this->assertEquals( 'draft', $this->zip->get_status() );

		$files = $this->zip->get_files();
		$this->assertCount( 3, $files );

		$this->assertEquals( 'abcde1234', $this->zip->get_gist_id() );
		$this->assertEquals( 'on', $this->zip->get_sync() );

		foreach ( $files as $file ) {
			$file_id = $file->get_ID();
			$this->assertStringStartsWith( 'third-slug-', $file->get_slug() );
			$this->assertStringStartsWith( 'console.log(', $file->get_code() );
			$this->assertEquals( 'js', $file->get_language()->get_slug() );
		}

		$history = $this->database->query( 'commit' )->history_by_head_id( $this->zip->get_ID() );

		$this->assertCount( 6, $history );

		$commit = $this->database->query( 'commit' )->latest_by_head_id( $this->zip->get_ID() );

		$this->assertEquals( 'Third Description', $commit->get_description() );
		$this->assertCount( 3, $commit->get_states() );
		$this->assertEquals( 'abcde1234', $commit->get_head_gist_id() );
		$this->assertEquals( 'on', $commit->get_sync() );

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
	}

	function tearDown() {
		parent::tearDown();
	}
}

class GistTest extends Gist {
	public function set_client($client) {
		$this->client = $client;
	}

	public function call() {
		return $this->client;
	}
}
