<?php
namespace Intraxia\Jaxion\Test\Http;

use Intraxia\Gistpen\Http\RepoController;
use Intraxia\Gistpen\Test\TestCase;
use Mockery;
use Mockery\MockInterface;

class RepoControllerTest extends TestCase {
	/**
	 * @var RepoController
	 */
	protected $controller;

	/**
	 * @var MockInterface
	 */
	protected $database;

	/**
	 * @var MockInterface
	 */
	protected $request;

	public function setUp() {
		parent::setUp();

		$this->controller = new RepoController( $this->database = $this->mock( 'database' ) );
		$this->request    = Mockery::mock( 'WP_REST_Request' );
	}

	public function test_should_return_collection_error_from_database() {
		$error = new \WP_Error;
		$this->request
			->shouldReceive( 'get_params' )
			->once()
			->withNoArgs()
			->andReturn( array() );
		$this->database
			->shouldReceive( 'find_by' )
			->with( 'Intraxia\Jaxion\Model\Repo' )
			->once()
			->with( RepoController::MODEL_CLASS, array() )
			->andReturn( $error );

		$this->assertSame( $error, $this->controller->index( $this->request ) );
		$this->assertEquals( array( 'status' => 500 ), $error->get_error_data() );
	}

	public function test_should_return_collection_in_response() {
		$collection = Mockery::mock( 'Intraxia\Jaxion\Axolotl\Collection' );
		$attrs = array( array( 'description' => 'Repo description' ) );
		$this->request
			->shouldReceive( 'get_params' )
			->once()
			->withNoArgs()
			->andReturn( array() );
		$this->database
			->shouldReceive( 'find_by' )
			->with( 'Intraxia\Gistpen\Model\Repo', array() )
			->once()
			->andReturn( $collection );
		$collection
			->shouldReceive( 'serialize' )
			->once()
			->andReturn( $attrs );

		$response = $this->controller->index( $this->request );

		$this->assertInstanceOf( 'WP_REST_Response', $response );
		$this->assertSame( $attrs, $response->get_data() );
	}

	public function test_should_return_model_error_from_database() {
		$error = new \WP_Error;
		$this->request
			->shouldReceive( 'get_param' )
			->once()
			->with( 'id' )
			->andReturn( 1 );
		$this->database
			->shouldReceive( 'find' )
			->with( 'Intraxia\Jaxion\Model\Repo' )
			->once()
			->with( RepoController::MODEL_CLASS, 1 )
			->andReturn( $error );

		$this->assertSame( $error, $this->controller->view( $this->request ) );
		$this->assertEquals( array( 'status' => 404 ), $error->get_error_data() );
	}

	public function test_should_return_repo_in_response() {
		$repo = Mockery::mock( 'Intraxia\Gistpen\Model\Repo' );
		$attrs = array( 'description' => 'Repo description' );
		$this->request
			->shouldReceive( 'get_param' )
			->once()
			->with( 'id' )
			->andReturn( 1 );
		$this->database
			->shouldReceive( 'find' )
			->with( 'Intraxia\Gistpen\Model\Repo', 1 )
			->once()
			->andReturn( $repo );
		$repo
			->shouldReceive( 'serialize' )
			->once()
			->andReturn( $attrs );

		$response = $this->controller->view( $this->request );

		$this->assertInstanceOf( 'WP_REST_Response', $response );
		$this->assertSame( $attrs, $response->get_data() );
	}
}
