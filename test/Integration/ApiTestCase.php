<?php
namespace Intraxia\Gistpen\Test\Integration;

use Intraxia\Gistpen\Test\Unit\TestCase;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;

abstract class ApiTestCase extends TestCase {
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

	public function set_role( $role ) {
		$post = $_POST;
		$user_id = $this->factory->user->create( array( 'role' => $role ) );
		wp_set_current_user( $user_id );
		$_POST = array_merge( $_POST, $post );
	}

	protected function assertResponseStatus( $status, WP_REST_Response $response ) {
		$this->assertSame( $status, $response->get_status() );
	}

	protected function assertResponseData( $data, WP_REST_Response $response ) {
		$response_data = $response->get_data();
		$tested_data = array();
		foreach( $data as $key => $value ) {
			if ( isset( $response_data[ $key ] ) ) {
				$tested_data[ $key ] = $response_data[ $key ];
			} else {
				$tested_data[ $key ] = null;
			}
		}
		$this->assertSame( $data, $tested_data );
	}

	protected function assertResponseHeader( $header, $value, WP_REST_Response $response ) {
		$headers = $response->get_headers();

		$this->assertSame( isset( $headers[ $header ] ) ? $headers[ $header ] : null, $value );
	}
}
