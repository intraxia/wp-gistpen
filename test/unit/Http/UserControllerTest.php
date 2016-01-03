<?php
namespace Intraxia\Jaxion\Test\Http;

use Intraxia\Gistpen\Http\UserController;
use InvalidArgumentException;
use Mockery;
use WP_Error;
use WP_UnitTestCase;

class UserControllerTest extends WP_UnitTestCase {
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
		$this->user       = Mockery::mock( 'Intraxia\Gistpen\Options\User' );
		$this->controller = new UserController( $this->user );
		$this->request    = Mockery::mock( 'WP_REST_Request' );
	}

	public function test_should_return_all_options_in_response() {
		$this->user
			->shouldReceive( 'all' )
			->once()
			->andReturn( $this->data );

		$response = $this->controller->view();

		$this->assertInstanceOf( 'WP_REST_Response', $response );
		$this->assertEquals( $this->data, $response->get_data() );
	}

	public function test_should_update_option_and_return_all() {
		$this->request
			->shouldReceive( 'get_params' )
			->once()
			->andReturn( $this->data );
		$this->user
			->shouldReceive( 'set' )
			->with( 'ace_theme', 'test' )
			->once();
		$this->user
			->shouldReceive( 'all' )
			->once()
			->andReturn( $this->data );

		$response = $this->controller->update( $this->request );

		$this->assertInstanceOf( 'WP_REST_Response', $response );
		$this->assertEquals( array_merge( array( 'errors' => array() ), $this->data ), $response->get_data() );
	}

	public function test_should_return_error_when_update_fails() {
		$this->request
			->shouldReceive( 'get_params' )
			->once()
			->andReturn( $this->data );
		$this->user
			->shouldReceive( 'set' )
			->with( 'ace_theme', 'test' )
			->once()
			->andThrow( new InvalidArgumentException );
		$this->user
			->shouldReceive( 'all' )
			->once()
			->andReturn( $this->data );
		$errors = array( new WP_Error( 'invalid_key', __( 'Invalid key', 'wp-gistpen' ), 'ace_theme' ) );

		$response = $this->controller->update( $this->request );

		$this->assertInstanceOf( 'WP_REST_Response', $response );
		$this->assertEquals( array_merge( array( 'errors' => $errors ), $this->data ), $response->get_data() );
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();
	}
}
