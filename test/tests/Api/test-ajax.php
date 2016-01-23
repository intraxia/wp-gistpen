<?php

use WP_Gistpen\Controller\Sync;
use WP_Gistpen\Facade\Database;
use WP_Gistpen\Facade\App;

/**
 * @group  api
 */
class WP_Gistpen_Api_Ajax_Test extends WP_Gistpen_UnitTestCase {

	public $user_id;
	public $response;
	public $posts = array();

	function set_correct_security() {
		$this->_setRole( 'administrator' );
		$_POST['nonce'] = wp_create_nonce( '_ajax_wp_gistpen' );
	}

	function check_response_failed() {
		$this->assertInternalType( 'object', $this->response );
		$this->assertObjectHasAttribute( 'success', $this->response );
		$this->assertFalse( $this->response->success );
	}

	function check_response_succeeded() {
		$this->assertInternalType( 'object', $this->response );
		$this->assertObjectHasAttribute( 'success', $this->response );
		$this->assertTrue( $this->response->success );
	}

	function setUp() {
		parent::setUp();
		$this->create_post_and_children();

		$this->database = new Database();
	}

	function test_failed_no_nonce() {
		try {
			$this->_handleAjax( 'get_gistpens' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_response_failed();

		$this->assertEquals( "Nonce check failed.", $this->response->data->message );
	}

	function test_failed_no_perms() {
		// Self-note: MUST set the role first or nonce check will fail
		$this->_setRole( 'subscriber' );
		$_POST['nonce'] = wp_create_nonce( '_ajax_wp_gistpen' );

		try {
			$this->_handleAjax( 'get_gistpens' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_response_failed();

		$this->assertEquals( "User doesn't have proper permisissions.", $this->response->data->message );
	}

	function test_succeeded_recent_gistpens() {
		$this->set_correct_security();
		$_POST['gistpen_search_term'] = '';

		try {
			$this->_handleAjax( 'get_gistpens' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_response_succeeded();
		$this->assertCount( 4, $this->response->data->gistpens );
	}

	function test_succeeded_search_returns_response() {
		$this->set_correct_security();
		$_POST['gistpen_search_term'] = 'Post title 2';

		try {
			$this->_handleAjax( 'get_gistpens' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_response_succeeded();
		$this->assertCount( 1, $this->response->data->gistpens );
	}

	function test_succeeded_search_returns_empty() {
		$this->set_correct_security();
		$_POST['gistpen_search_term'] = 'asdf';

		try {
			$this->_handleAjax( 'get_gistpens' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_response_succeeded();
		$this->assertCount( 0, $this->response->data->gistpens );
	}

	function test_get_gistpen() {
		$this->set_correct_security();
		$_POST['post_id'] = $this->gistpen->ID;

		try {
			$this->_handleAjax( 'get_gistpen' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_response_succeeded();
		$this->assertInstanceOf( 'stdClass', $this->response->data );
		$this->assertInternalType( 'array', $this->response->data->files );
	}

	function test_succeeded_gistpen_creation() {
		$this->set_correct_security();
		$_POST['wp-gistpenfile-slug'] = 'New Gistpen';
		$_POST['wp-gistfile-description'] = 'New Gistpen Description';
		$_POST['wp-gistpenfile-code'] = 'echo $stuff;';
		$_POST['post_status'] = 'draft';
		$_POST['wp-gistpenfile-language'] = 'php';

		try {
			$this->_handleAjax( 'create_gistpen' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_response_succeeded();

		$this->assertObjectHasAttribute( 'id', $this->response->data );

		$this->assertInternalType( 'integer', $this->response->data->id );
		$this->assertTrue( $this->response->data->id !== 0 );

		$zip = $this->database->query()->by_id( $this->response->data->id );

		$this->assertInstanceOf( 'WP_Gistpen\Model\Zip', $zip );
		$this->assertEquals( 'New Gistpen Description', $zip->get_description() );
		$this->assertEquals( 'draft', $zip->get_status() );

		$files = $zip->get_files();
		$this->assertCount( 1, $files );

		$file = array_pop( $files );
		$this->assertEquals( 'php', $file->get_language()->get_slug() );
	}

	function test_succeeded_save_gistpen() {
		$this->set_correct_security();
		$_POST['zip'] = array(
			'description'  => 'New Gistpen Description',
			'status'       => 'auto-draft',
			'ID'           => null,
			'files'        => array(
				array(
					'slug'     => 'New Gistpen',
					'code'     => 'echo $stuff;',
					'ID'       => null,
					'language' => 'php',
				),
			),
		);

		try {
			$this->_handleAjax( 'save_gistpen' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_response_succeeded();
		$this->assertEquals( 'updated', $this->response->data->code );
		$this->assertContains( 'Successfully updated Gistpen', $this->response->data->message );
	}

	function test_succeeded_get_theme() {
		$this->set_correct_security();
		update_user_meta( get_current_user_id(), '_wpgp_ace_theme', 'testtheme' );

		try {
			$this->_handleAjax( 'get_ace_theme' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_response_succeeded();
		$this->assertEquals( 'testtheme', $this->response->data->theme );
	}

	function test_succeeded_save_theme() {
		$this->set_correct_security();
		$_POST['theme'] = 'twilight';

		try {
			$this->_handleAjax( 'save_ace_theme' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_response_succeeded();
		$this->assertEquals( 'twilight', get_user_meta( get_current_user_id(), '_wpgp_ace_theme', true ) );
	}

	function test_gistpens_missing_gist_id() {
		App::get('ajax')->database = $this->mock_database;
		App::get('ajax')->sync = $this->mock_sync;

		$this->set_correct_security();

		$this->mock_database->
			shouldReceive( 'query' )
			->times( 1 )
			->andReturn( $this->mock_database )
			->shouldReceive( 'missing_gist_id' )
			->times( 1 )
			->andReturn( array( $this->gistpen->ID ) );

		try {
			$this->_handleAjax( 'get_gistpens_missing_gist_id' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_response_succeeded();
		$this->assertObjectHasAttribute( 'ids', $this->response->data );
		$this->assertInternalType( 'array', $this->response->data->ids );
		$this->assertCount( 1, $this->response->data->ids );
	}

	function test_create_gist_from_gistpen_id() {
		App::get('ajax')->database = $this->mock_database;
		App::get('ajax')->sync = $this->mock_sync;
		$this->mock_database->
			shouldReceive( 'persist' )
			->times( 1 )
			->andReturn( $this->mock_database )
			->shouldReceive( 'set_sync' )
			->once();
		$this->mock_sync
			->shouldReceive( 'export_gistpen' )
			->times( 1 )
			->with( $this->gistpen->ID )
			->andReturn( $this->gistpen->ID );

		$this->set_correct_security();

		$_POST['gistpen_id'] = $this->gistpen->ID;

		try {
			$this->_handleAjax( 'create_gist_from_gistpen_id' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_response_succeeded();
		$this->assertObjectHasAttribute( 'code', $this->response->data );
		$this->assertEquals( 'success', $this->response->data->code );
		$this->assertObjectHasAttribute( 'message', $this->response->data );
	}

	function tearDown() {
		parent::tearDown();

		App::get('ajax')->database = new Database();
		App::get('ajax')->sync = new Sync();
	}
}
