<?php
namespace Intraxia\Gistpen\Test\Integration\Http\Site;

use WP_REST_Request;
use Intraxia\Gistpen\Options\Site;
use Intraxia\Gistpen\Test\Integration\TestCase;

class ResourceTest extends TestCase {
	public function test_returns_401_without_perms() {
		$this->set_role( 'subscriber' );
		$request = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/site' );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 401 );
		$this->assertResponseData( $response, [
			'code'    => 'unauthorized',
			'message' => 'Unauthorized user',
			'data'    => [
				'status' => 401,
			],
		] );
	}

	public function test_returns_site_options() {
		$this->set_role( 'administrator' );
		$request = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/site' );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 200 );
		$this->assertResponseData( $response, [
			'prism' => [
				'theme'           => 'default',
				'line-numbers'    => false,
				'show-invisibles' => false,
			],
			'gist'  => [
				'token' => '',
			],
		] );
	}
}
