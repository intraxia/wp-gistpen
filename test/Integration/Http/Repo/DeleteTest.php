<?php
namespace Intraxia\Gistpen\Test\Integration\Http\Repo;

use Intraxia\Gistpen\Model\Repo;
use Intraxia\Gistpen\Test\Integration\TestCase;
use Intraxia\Jaxion\Contract\Axolotl\EntityManager as EM;
use WP_REST_Request;

class DeleteTest extends TestCase {
	public function test_requires_admin() {
		$this->set_role( 'subscriber' );
		$request = new WP_REST_Request( 'DELETE', '/intraxia/v1/gistpen/repos/123' );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 401 );
	}

	public function test_returns_404() {
		$this->set_role( 'administrator' );
		$request = new WP_REST_Request( 'DELETE', '/intraxia/v1/gistpen/repos/123' );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 404 );
		$this->assertResponseData( $response, [
			'code'    => 'invalid_data',
			'message' => 'post id 123 is invalid',
			'data'    => [
				'status' => 404,
			],
		] );
	}

	public function test_deletes_repo() {
		$this->set_role( 'administrator' );
		$repo    = $this->fm->create( Repo::class );
		$request = new WP_REST_Request( 'DELETE', '/intraxia/v1/gistpen/repos/' . $repo->ID );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 204 );
		// Check deleted
		$this->assertWPError( $this->app->get( EM::class )->find( Repo::class, $repo->ID ) );
	}
}
