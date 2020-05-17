<?php
namespace Intraxia\Gistpen\Test\Integration\Http\Site;

use WP_REST_Request;
use Intraxia\Gistpen\Options\Site;
use Intraxia\Gistpen\Test\Integration\TestCase;

class PatchTest extends TestCase {
	public function test_should_error_on_invalid_key() {
		$this->set_role( 'administrator' );
		$request = new WP_REST_Request( 'PATCH', '/intraxia/v1/gistpen/site' );
		$request->set_body_params( [
			'invalid' => 'key',
		] );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 400 );
		$this->assertResponseData( $response, [
			'code'    => 'rest_invalid_param',
			'message' => 'Invalid parameter(s): invalid',
			'data'    => [
				'status' => 400,
				'params' => [
					'invalid' => 'invalid is not a valid request param.',
				],
			],
		] );
	}

	public function test_should_error_on_invalid_gist_key() {
		$this->set_role( 'administrator' );
		$request = new WP_REST_Request( 'PATCH', '/intraxia/v1/gistpen/site' );
		$request->set_body_params( [
			'gist' => [
				'invalid' => 'key',
			],
		] );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 400 );
		$this->assertResponseData( $response, [
			'code'    => 'rest_invalid_param',
			'message' => 'Invalid parameter(s): gist',
			'data'    => [
				'status' => 400,
				'params' => [
					'gist' => 'Param "gist.invalid" is not a valid request param.',
				],
			],
		] );
	}

	public function test_should_error_on_invalid_gist_token_type() {
		$this->set_role( 'administrator' );
		$request = new WP_REST_Request( 'PATCH', '/intraxia/v1/gistpen/site' );
		$request->set_body_params( [
			'gist' => [
				'token' => 123,
			],
		] );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 400 );
		$this->assertResponseData( $response, [
			'code'    => 'rest_invalid_param',
			'message' => 'Invalid parameter(s): gist',
			'data'    => [
				'status' => 400,
				'params' => [
					'gist' => 'Param "gist.token" is not a string.',
				],
			],
		] );
	}

	public function test_should_update_gist_token() {
		$this->set_role( 'administrator' );
		$request = new WP_REST_Request( 'PATCH', '/intraxia/v1/gistpen/site' );
		$request->set_body_params( [
			'gist' => [
				'token' => '123',
			],
		] );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 200 );
		$this->assertResponseData( $response, [
			'prism' => [
				'theme'           => 'default',
				'line-numbers'    => false,
				'show-invisibles' => false,
			],
			'gist'  => [
				'token' => '123',
			],
		] );
	}

	public function test_should_error_on_invalid_prism_key() {
		$this->set_role( 'administrator' );
		$request = new WP_REST_Request( 'PATCH', '/intraxia/v1/gistpen/site' );
		$request->set_body_params( [
			'prism' => [
				'invalid' => 'key',
			],
		] );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 400 );
		$this->assertResponseData( $response, [
			'code'    => 'rest_invalid_param',
			'message' => 'Invalid parameter(s): prism',
			'data'    => [
				'status' => 400,
				'params' => [
					'prism' => 'Param "prism.invalid" is not a valid request param.',
				],
			],
		] );
	}

	public function test_should_error_on_invalid_prism_theme_type() {
		$this->set_role( 'administrator' );
		$request = new WP_REST_Request( 'PATCH', '/intraxia/v1/gistpen/site' );
		$request->set_body_params( [
			'prism' => [
				'theme' => 123,
			],
		] );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 400 );
		$this->assertResponseData( $response, [
			'code'    => 'rest_invalid_param',
			'message' => 'Invalid parameter(s): prism',
			'data'    => [
				'status' => 400,
				'params' => [
					'prism' => 'Param "prism.theme" is not a string.',
				],
			],
		] );
	}

	public function test_should_error_on_invalid_prism_theme_name() {
		$this->set_role( 'administrator' );
		$request = new WP_REST_Request( 'PATCH', '/intraxia/v1/gistpen/site' );
		$request->set_body_params( [
			'prism' => [
				'theme' => 'invalid-theme',
			],
		] );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 400 );
		$this->assertResponseData( $response, [
			'code'    => 'rest_invalid_param',
			'message' => 'Invalid parameter(s): prism',
			'data'    => [
				'status' => 400,
				'params' => [
					'prism' => 'Param "prism.theme" is not a valid theme.',
				],
			],
		] );
	}

	public function test_should_update_prism_theme() {
		$this->set_role( 'administrator' );
		$request = new WP_REST_Request( 'PATCH', '/intraxia/v1/gistpen/site' );
		$request->set_body_params( [
			'prism' => [
				'theme' => 'xonokai',
			],
		] );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 200 );
		$this->assertResponseData( $response, [
			'prism' => [
				'theme'           => 'xonokai',
				'line-numbers'    => false,
				'show-invisibles' => false,
			],
			'gist'  => [
				'token' => '',
			],
		] );
	}

	public function test_should_error_on_invalid_prism_ln_type() {
		$this->set_role( 'administrator' );
		$request = new WP_REST_Request( 'PATCH', '/intraxia/v1/gistpen/site' );
		$request->set_body_params( [
			'prism' => [
				'line-numbers' => 'string',
			],
		] );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 400 );
		$this->assertResponseData( $response, [
			'code'    => 'rest_invalid_param',
			'message' => 'Invalid parameter(s): prism',
			'data'    => [
				'status' => 400,
				'params' => [
					'prism' => 'Param "prism.line-numbers" is not a boolean.',
				],
			],
		] );
	}

	public function test_should_update_prism_ln() {
		$this->set_role( 'administrator' );
		$request = new WP_REST_Request( 'PATCH', '/intraxia/v1/gistpen/site' );
		$request->set_body_params( [
			'prism' => [
				'line-numbers' => true,
			],
		] );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 200 );
		$this->assertResponseData( $response, [
			'prism' => [
				'theme'           => 'default',
				'line-numbers'    => true,
				'show-invisibles' => false,
			],
			'gist'  => [
				'token' => '',
			],
		] );
	}

	public function test_should_error_on_invalid_prism_si() {
		$this->set_role( 'administrator' );
		$request = new WP_REST_Request( 'PATCH', '/intraxia/v1/gistpen/site' );
		$request->set_body_params( [
			'prism' => [
				'show-invisibles' => 'string',
			],
		] );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 400 );
		$this->assertResponseData( $response, [
			'code'    => 'rest_invalid_param',
			'message' => 'Invalid parameter(s): prism',
			'data'    => [
				'status' => 400,
				'params' => [
					'prism' => 'Param "prism.show-invisibles" is not a boolean.',
				],
			],
		] );
	}

	public function test_should_update_prism_si() {
		$this->set_role( 'administrator' );
		$request = new WP_REST_Request( 'PATCH', '/intraxia/v1/gistpen/site' );
		$request->set_body_params( [
			'prism' => [
				'show-invisibles' => true,
			],
		] );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 200 );
		$this->assertResponseData( $response, [
			'prism' => [
				'theme'           => 'default',
				'line-numbers'    => false,
				'show-invisibles' => true,
			],
			'gist'  => [
				'token' => '',
			],
		] );
	}
}
