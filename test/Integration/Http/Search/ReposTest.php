<?php
namespace Intraxia\Gistpen\Test\Integration\Http\Search;

use WP_REST_Request;
use Intraxia\Gistpen\Model\Blob;
use Intraxia\Gistpen\Model\Repo;
use Intraxia\Gistpen\Test\Integration\TestCase;

class ReposTest extends TestCase {
	public function setUp() {
		parent::setUp();

		$this->repo        = $this->fm->create( Repo::class, [ 'status' => 'publish' ] );
		$this->blob        = $this->fm->create( Blob::class, [ 'repo_id' => $this->repo->ID ] );
		$this->repo->blobs = $this->repo->blobs->add( $this->blob );

		$this->repo_output = [
			'ID'          => $this->repo->ID,
			'description' => $this->repo->description,
			'slug'        => $this->repo->slug,
			'status'      => $this->repo->status,
			'password'    => $this->repo->password,
			'gist_id'     => $this->repo->gist_id,
			'gist_url'    => $this->repo->gist_url,
			'sync'        => $this->repo->sync,
			'blobs'       => [
				[
					'ID'       => $this->blob->ID,
					'filename' => $this->blob->filename,
					'rest_url' => $this->blob->rest_url,
				],
			],
			'rest_url'    => $this->repo->rest_url,
			'commits_url' => $this->repo->commits_url,
			'html_url'    => $this->repo->html_url,
			'created_at'  => $this->repo->created_at,
			'updated_at'  => $this->repo->updated_at,
		];
	}

	public function test_should_return_error_on_extra_param() {
		$request = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/search/repos' );
		$request->set_query_params( [ 'invalid' => 'unknown' ] );

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

	public function test_should_return_repo_only_by_default() {
		$request = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/search/repos' );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 200 );
		$this->assertResponseData( $response, [ $this->repo_output ] );
	}

	public function test_should_search_for_existing_repo() {
		$request = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/search/repos' );
		$request->set_query_params( [ 's' => $this->repo->description ] );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 200 );
		$this->assertResponseData( $response, [ $this->repo_output ] );
	}

	public function test_should_not_return_existing_repo_if_search_doesnt_match() {
		$request = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/search/repos' );
		$request->set_query_params( [ 's' => 'asdfjalsdfjalskdjfalsdfjkasdflaksjdflaksjdfl' ] );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 200 );
		$this->assertResponseData( $response, [] );
	}
}
