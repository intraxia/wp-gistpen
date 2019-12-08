<?php
namespace Intraxia\Gistpen\Test\Integration\Http\Repo;

use WP_REST_Request;
use Intraxia\Gistpen\Model\Repo;
use Intraxia\Gistpen\Test\Integration\TestCase;

class ResourceTest extends TestCase {
	public function test_returns_404() {
		$request = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/repos/123' );

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

	public function test_returns_repo() {
		$repo    = $this->fm->create( Repo::class );
		$request = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/repos/' . $repo->ID );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 200 );
		$this->assertResponseData( $response, [
			'ID'          => $repo->ID,
			'description' => $repo->description,
			'slug'        => $repo->slug,
			'status'      => $repo->status,
			'password'    => $repo->password,
			'gist_id'     => $repo->gist_id,
			'gist_url'    => $repo->gist_url,
			'sync'        => $repo->sync,
			'blobs'       => [],
			'rest_url'    => $repo->rest_url,
			'commits_url' => $repo->commits_url,
			'html_url'    => $repo->html_url,
			'created_at'  => $repo->created_at,
			'updated_at'  => $repo->updated_at,
		] );
	}
}
