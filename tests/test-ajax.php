<?php

/**
 * @group  ajax
 */
class WP_Gistpen_AJAX_Test extends WP_Ajax_UnitTestCase {

	public $user_id;

	function set_correct_security() {
		$this->_setRole( 'administrator' );
		$_POST['nonce'] = wp_create_nonce( WP_Gistpen_AJAX::$nonce_field );
	}

	function setUp() {
		parent::setUp();
		$this->user_id = $this->factory->user->create();
		$this->factory->post->create_many( 10, array(
			'post_type' => 'gistpens',
			'post_author' => $this->user_id,
			'post_status' => 'publish',
			'post_title' => new WP_UnitTest_Generator_Sequence( 'Post title %s' ),
			'post_content' => new WP_UnitTest_Generator_Sequence( 'Post content %s' ),
			'post_excerpt' => new WP_UnitTest_Generator_Sequence( 'Post excerpt %s' )
		));
	}

	function test_fails_without_nonce() {
		try {
			$this->_handleAjax( 'get_recent_gistpens' );
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
			$this->_handleAjax( 'get_recent_gistpens' );
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
			$this->_handleAjax( 'get_recent_gistpens' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$response = json_decode($this->_last_response);

		$this->assertInternalType( 'object', $response );
		$this->assertObjectHasAttribute( 'success', $response );
		$this->assertTrue( $response->success );
		$this->assertCount( 5, $response->data->gistpens, 'message' );
	}

	function tearDown() {
		parent::tearDown();
	}
}
