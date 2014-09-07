<?php

/**
 * @group  ajax
 */
class WP_Gistpen_AJAX_Test extends WP_Ajax_UnitTestCase {

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
		$this->user_id = $this->factory->user->create();
		$this->posts = $this->factory->post->create_many( 10, array(
			'post_type' => 'gistpens',
			'post_author' => $this->user_id,
			'post_status' => 'publish',
		), array(
			'post_title' => new WP_UnitTest_Generator_Sequence( 'AJAX Post title %s' ),
			'post_content' => new WP_UnitTest_Generator_Sequence( 'AJAX Post content %s' ),
			'post_excerpt' => new WP_UnitTest_Generator_Sequence( 'AJAX Post excerpt %s' )
		));
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

	function test_returns_gistpen_languages() {
		$this->set_correct_security();

		try {
			$this->_handleAjax( 'get_gistpen_languages' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_standard_response_info();
		$this->assertObjectHasAttribute( 'languages', $this->response->data );
		$this->assertInternalType( 'object', $this->response->data->languages );
	}

	function test_returns_recent_gistpens() {
		$this->set_correct_security();

		try {
			$this->_handleAjax( 'get_gistpens' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_standard_response_info();
		$this->assertCount( 5, $this->response->data->gistpens );
	}

	function test_returns_gistpens_with_search() {
		$this->set_correct_security();
		$_POST['gistpen_search_term'] = 'Post title 9';

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
		$_POST['wp-gistpenfile-content'] = '<?php echo $stuff; ?>';
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

	function test_add_ace_editor() {
		$this->set_correct_security();

		try {
			$this->_handleAjax( 'add_gistfile_editor' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_standard_response_info();
		$this->assertObjectHasAttribute( 'id', $this->response->data );
		$this->assertInternalType( 'integer', $this->response->data->id );
		$this->assertTrue( $this->response->data->id !== 0 );
	}

	function test_delete_ace_editor() {
		$this->set_correct_security();
		$_POST['fileID'] = $this->posts[0];

		try {
			$this->_handleAjax( 'delete_gistfile_editor' );
		} catch ( WPAjaxDieContinueException $e ) {}
		$this->response = json_decode($this->_last_response);

		$this->check_standard_response_info();
	}

	function tearDown() {
		parent::tearDown();
	}
}
