<?php
namespace Intraxia\Gistpen\Test\Integration;

use Intraxia\Gistpen\Test\TestCase as BaseTestCase;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;

abstract class TestCase extends BaseTestCase {
	/**
	 * @var WP_REST_Server
	 */
	protected $server;

	public function setUp() {
		parent::setUp();

		global $wp_rest_server;
		$this->server = $wp_rest_server = new WP_REST_Server;
		do_action( 'plugins_loaded' );
		do_action( 'rest_api_init' );

		$this->set_permalink_structure( '/%postname%/' );
	}

	public function tearDown() {
		parent::tearDown();

		global $wp_rest_server;
		$wp_rest_server = null;
	}

	protected function assertResponseStatus( WP_REST_Response $response, $status ) {
		$this->assertSame( $status, $response->get_status() );
	}

	protected function assertResponseData( WP_REST_Response $response, $data ) {
		$this->assertSame( $data, $response->get_data() );
	}

	protected function assertResponseHeader( WP_REST_Response $response, $header, $value ) {
		$headers = $response->get_headers();

		$this->assertSame( isset( $headers[ $header ] ) ? $headers[ $header ] : null, $value );
	}
}
