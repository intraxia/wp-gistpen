<?php
namespace Intraxia\Gistpen\Test\Http;

use Intraxia\Gistpen\Http\UserController;
use Intraxia\Gistpen\Test\TestCase;
use InvalidArgumentException;
use Mockery;
use WP_Error;
use WP_UnitTestCase;

class UserControllerTest extends TestCase {
	/**
	 * @var UserController
	 */
	protected $controller;

	/**
	 * @var Mockery\MockInterface
	 */
	protected $user;

	/**
	 * @var Mockery\MockInterface
	 */
	protected $request;

	protected $data = array( 'ace_theme' => 'test' );

	public function setUp() {
		parent::setUp();
		$this->controller = new UserController( $this->user = $this->mock( 'options.user' ) );
		$this->request    = $this->mock( 'WP_REST_Request' );
	}

	public function test_should_return_all_options_in_response() {
		$this->user
			->shouldReceive( 'all' )
			->once()
			->andReturn( $this->data );

		$response = $this->controller->view();

		$this->assertInstanceOf( 'WP_REST_Response', $response );
		$this->assertSame( $this->data, $response->get_data() );
	}

	public function test_should_update_option_and_return_all() {
		$this->request
			->shouldReceive( 'get_params' )
			->once()
			->andReturn( $this->data );
		$this->user
			->shouldReceive( 'patch' )
			->with( $this->data )
			->once()
			->andReturn( $this->data );

		$response = $this->controller->update( $this->request );

		$this->assertInstanceOf( 'WP_REST_Response', $response );
		$this->assertSame( $this->data, $response->get_data() );
		$this->assertSame( 200, $response->get_status() );
	}

	public function test_should_return_invalid_key_when_update_fails() {
		$this->request
			->shouldReceive( 'get_params' )
			->once()
			->andReturn( $this->data );
		$this->user
			->shouldReceive( 'patch' )
			->with( $this->data )
			->once()
			->andThrow( new InvalidArgumentException );

		/** @var WP_Error $response */
		$response = $this->controller->update( $this->request );

		$this->assertInstanceOf( 'WP_Error', $response );
		$this->assertSame( array( 'status' => 400 ), $response->get_error_data() );
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();
	}
}
