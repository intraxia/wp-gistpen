<?php

/**
 * @group  ajax
 */
class WP_Gistpen_AJAX_Test extends WP_Gistpen_UnitTestCase {

	public $user_id;
	public $response;
	public $posts = array();

	function set_correct_security() {
		$this->_setRole( 'administrator' );
		$_POST['nonce'] = wp_create_nonce( WP_Gistpen_AJAX::$nonce_field );
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
	}

	function test_failed_no_nonce() {
		try {
			$this->_handleAjax( 'get_gistpens' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_response_failed();

		$this->assertEquals( "Nonce check failed.", $this->response->data->error );
	}

	function test_failed_no_perms() {
		// Self-note: MUST set the role first or nonce check will fail
		$this->_setRole( 'subscriber' );
		$_POST['nonce'] = wp_create_nonce( WP_Gistpen_AJAX::$nonce_field );

		try {
			$this->_handleAjax( 'get_gistpens' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_response_failed();

		$this->assertEquals( "User doesn't have proper permisissions.", $this->response->data->error );
	}

	function test_succeeded_recent_gistpens() {
		$this->set_correct_security();

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

		WP_Gistpen::get_instance()->query = $this->mock_query;
		$this->mock_query
			->expects( $this->once() )
			->method( 'search' )
			->will( $this->returnValue( true ) );

		try {
			$this->_handleAjax( 'get_gistpens' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_response_succeeded();
	}

	function test_failed_search_returns_error() {
		$this->set_correct_security();

		WP_Gistpen::get_instance()->query = $this->mock_query;
		$this->mock_query
			->expects( $this->once() )
			->method( 'search' )
			->will( $this->returnValue( new WP_Error ) );

		try {
			$this->_handleAjax( 'get_gistpens' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_response_failed();
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

		$zip = WP_Gistpen::get_instance()->query->get( $this->response->data->id );

		$this->assertNotInstanceOf( 'WP_Error', $zip );
		$this->assertEquals( 'New Gistpen Description', $zip->description );
		$this->assertEquals( 'draft', $zip->post->post_status );
		$this->assertCount( 1, $zip->files );
		$this->assertEquals( 'php', array_pop( $zip->files )->language->slug );
	}

	// @todo list requirements? Is that necessary? Should we test "Need everything below"?
	function test_failed_gistpen_creation() {
		$this->set_correct_security();
		$_POST['wp-gistpenfile-slug'] = 'New Gistpen';
		$_POST['wp-gistfile-description'] = 'New Gistpen Description';
		$_POST['wp-gistpenfile-code'] = 'echo $stuff;';
		$_POST['post_status'] = 'draft';
		$_POST['wp-gistpenfile-language'] = 'php';

		WP_Gistpen::get_instance()->query = $this->mock_query;
		$this->mock_query
			->expects( $this->once() )
			->method( 'save' )
			->will( $this->returnValue( new WP_Error ) );

		try {
			$this->_handleAjax( 'create_gistpen' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_response_failed();
	}

	// @todo Requirements again?
	function test_succeeded_save_theme() {
		$this->set_correct_security();
		$_POST['theme'] = 'twilight';

		try {
			$this->_handleAjax( 'save_ace_theme' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_response_succeeded();
	}

	function test_failed_without_parent() {
		$this->set_correct_security();

		try {
			$this->_handleAjax( 'get_gistpenfile_id' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_response_failed();
	}

	function test_failed_query_new_id() {
		$this->set_correct_security();
		$_POST['parent_id'] = $this->gistpen->ID;

		WP_Gistpen::get_instance()->query = $this->mock_query;
		$this->mock_query
			->expects( $this->once() )
			->method( 'save' )
			->will( $this->returnValue( new WP_Error ) );

		try {
			$this->_handleAjax( 'get_gistpenfile_id' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_response_failed();
	}

	function test_succeeded_get_new_id() {
		$this->set_correct_security();
		$_POST['parent_id'] = $this->gistpen->ID;

		try {
			$this->_handleAjax( 'get_gistpenfile_id' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_response_succeeded();
		$this->assertObjectHasAttribute( 'id', $this->response->data );
		$this->assertInternalType( 'integer', $this->response->data->id );
		$this->assertTrue( $this->response->data->id !== 0 );
	}

	function test_failed_delete_file_needs_id() {
		$this->set_correct_security();

		try {
			$this->_handleAjax( 'delete_gistpenfile' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_response_failed();
	}

	function test_succeeded_delete_file() {
		$this->set_correct_security();
		$_POST['fileID'] = $this->files[0];

		try {
			$this->_handleAjax( 'delete_gistpenfile' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_response_succeeded();
	}

	function tearDown() {
		parent::tearDown();

		WP_Gistpen::get_instance()->query = new WP_Gistpen_Query;
	}
}
