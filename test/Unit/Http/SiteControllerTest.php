<?php
namespace Intraxia\Gistpen\Test\Unit\Http;

use Intraxia\Gistpen\Http\SiteController;
use Intraxia\Gistpen\Test\Unit\TestCase;
use InvalidArgumentException;
use Mockery;

class SiteControllerTest extends TestCase {
	/**
	 * @var SiteController
	 */
	protected $controller;

	/**
	 * @var Mockery\MockInterface
	 */
	protected $site;

	/**
	 * @var Mockery\MockInterface
	 */
	protected $request;

	protected $data = array( 'prism' => array( 'key' => 'value' ) );

	public function setUp() {
		parent::setUp();
		$this->controller = new SiteController( $this->site = $this->mock( 'options.site' ) );
		$this->request    = $this->mock( 'WP_REST_Request' );
	}

	public function test_should_return_all_options_in_response() {
		$this->site
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
		$this->site
			->shouldReceive( 'patch' )
			->with( $this->data )
			->once();
		$this->site
			->shouldReceive( 'all' )
			->once()
			->andReturn( $this->data );

		$response = $this->controller->update( $this->request );

		$this->assertInstanceOf( 'WP_REST_Response', $response );
		$this->assertSame( $this->data, $response->get_data() );
		$this->assertSame( 200, $response->get_status() );
	}

	public function tearDown() {
		parent::tearDown();
		Mockery::close();
	}
}
