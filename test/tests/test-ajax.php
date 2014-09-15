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

	function check_standard_response_info() {
		$this->assertInternalType( 'object', $this->response );
		$this->assertObjectHasAttribute( 'success', $this->response );
		$this->assertTrue( $this->response->success );
	}

	function setUp() {
		parent::setUp();
		$this->create_post_and_children();
	}

	function test_fails_without_nonce() {
		try {
			$this->_handleAjax( 'get_gistpens' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$response = json_decode($this->_last_response);

		$this->assertInternalType( 'object', $response );
		$this->assertObjectHasAttribute( 'success', $response );
		$this->assertFalse( $response->success );
		$this->assertEquals( "Nonce check failed.", $response->data->error );
	}

	function test_fails_without_perms() {
		// Self-note: MUST set the role first or nonce check will fail
		$this->_setRole( 'subscriber' );
		$_POST['nonce'] = wp_create_nonce( WP_Gistpen_AJAX::$nonce_field );

		try {
			$this->_handleAjax( 'get_gistpens' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$response = json_decode($this->_last_response);

		$this->assertInternalType( 'object', $response );
		$this->assertObjectHasAttribute( 'success', $response );
		$this->assertFalse( $response->success );
		$this->assertEquals( "User doesn't have proper permisissions.", $response->data->error );
	}

	function test_returns_recent_gistpens() {
		$this->set_correct_security();

		try {
			$this->_handleAjax( 'get_gistpens' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_standard_response_info();
		$this->assertCount( 4, $this->response->data->gistpens );
	}

	function test_returns_gistpens_with_search() {
		$this->set_correct_security();
		$_POST['gistpen_search_term'] = 'Post title 2';

		try {
			$this->_handleAjax( 'get_gistpens' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_standard_response_info();
		$this->assertCount( 1, $this->response->data->gistpens );
	}

	function test_create_gistpen() {
		$this->set_correct_security();
		$_POST['wp-gistpenfile-name'] = 'New Gistpen';
		$_POST['wp-gistfile-description'] = 'New Gistpen Description';
		$_POST['wp-gistpenfile-content'] = 'echo $stuff;';
		$_POST['post_status'] = 'draft';
		$_POST['wp-gistpenfile-language'] = 'php';

		try {
			$this->_handleAjax( 'create_gistpen' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_standard_response_info();
		$this->assertObjectHasAttribute( 'id', $this->response->data );
		$this->assertInternalType( 'integer', $this->response->data->id );
		$this->assertTrue( $this->response->data->id !== 0 );
		$this->assertNotEquals( null, get_post( $this->response->data->id ) );
		$this->assertEquals( 'draft', get_post( $this->response->data->id )->post_status );
	}

	function test_save_ace_theme() {
		$this->set_correct_security();
		$_POST['theme'] = 'twilight';

		try {
			$this->_handleAjax( 'save_ace_theme' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_standard_response_info();
	}

	function test_get_gistpenfile_id() {
		$this->set_correct_security();
		$_POST['parent_id'] = $this->gistpen->ID;

		try {
			$this->_handleAjax( 'get_gistpenfile_id' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_standard_response_info();
		$this->assertObjectHasAttribute( 'id', $this->response->data );
		$this->assertInternalType( 'integer', $this->response->data->id );
		$this->assertTrue( $this->response->data->id !== 0 );
	}

	function test_delete_gistpenfile_editor() {
		$this->set_correct_security();
		$_POST['fileID'] = $this->files[0];

		try {
			$this->_handleAjax( 'delete_gistpenfile_editor' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_standard_response_info();
	}

	function tearDown() {
		parent::tearDown();
	}
}
