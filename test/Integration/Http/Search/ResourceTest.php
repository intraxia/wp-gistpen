<?php
namespace Intraxia\Gistpen\Test\Integration\Http\Search;

use WP_REST_Request;
use Intraxia\Gistpen\Model\Blob;
use Intraxia\Gistpen\Model\Repo;
use Intraxia\Gistpen\Test\Integration\TestCase;

class ResourceTest extends TestCase {
	public function test_should_return_error_on_extra_param() {
		$repo    = $this->fm->create( Repo::class, [ 'status' => 'publish' ] );
		$request = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/search' );
		$request->set_query_params( [ 'invalid' => 'unknown' ] );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 400 );
		$this->assertResponseData( $response, [
			'code'    => 'rest_invalid_param',
			'message' => 'Invalid parameter(s): invalid',
			'data'    => [
				'status' => 400,
				'params' => [
					'invalid' => 'Param "invalid" is not a valid request param.',
				],
			],
		] );
	}

	public function test_should_return_repo_only_by_default() {
		$repo        = $this->fm->create( Repo::class, [ 'status' => 'publish' ] );
		$blob        = $this->fm->create( Blob::class, [ 'repo_id' => $repo->ID ] );
		$repo->blobs = $repo->blobs->add( $blob );
		$request     = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/search' );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 200 );
		$this->assertResponseData( $response, [ $repo->serialize() ] );
	}

	public function test_return_error_if_invalid() {
		$repo    = $this->fm->create( Repo::class, [ 'status' => 'publish' ] );
		$request = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/search' );
		$request->set_query_params( [ 'type' => 'invalid' ] );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 400 );
		$this->assertResponseData( $response, [
			'code'    => 'rest_invalid_param',
			'message' => 'Invalid parameter(s): type',
			'data'    => [
				'status' => 400,
				'params' => [
					'type' => 'Invalid parameter.',
				],
			],
		] );
	}

	public function test_should_return_repo_when_selected() {
		$repo        = $this->fm->create( Repo::class, [ 'status' => 'publish' ] );
		$blob        = $this->fm->create( Blob::class, [ 'repo_id' => $repo->ID ] );
		$repo->blobs = $repo->blobs->add( $blob );
		$request     = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/search' );
		$request->set_query_params( [ 'type' => 'repo' ] );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 200 );
		$this->assertResponseData( $response, [ $repo->serialize() ] );
	}

	public function test_should_return_blob_when_selected() {
		$repo        = $this->fm->create( Repo::class, [ 'status' => 'publish' ] );
		$blob        = $this->fm->create( Blob::class, [ 'repo_id' => $repo->ID ] );
		$repo->blobs = $repo->blobs->add( $blob );
		$request     = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/search' );
		$request->set_query_params( [ 'type' => 'blob' ] );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 200 );
		$this->assertResponseData( $response, [ $blob->serialize() ] );
	}

	public function test_should_search_for_existing_repo() {
		$repo    = $this->fm->create( Repo::class, [ 'status' => 'publish' ] );
		$request = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/search' );
		$request->set_query_params( [ 's' => $repo->description ] );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 200 );
		$this->assertResponseData( $response, [ $repo->serialize() ] );
	}

	public function test_should_not_return_existing_repo_if_search_doesnt_match() {
		$repo    = $this->fm->create( Repo::class, [ 'status' => 'publish' ] );
		$request = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/search' );
		$request->set_query_params( [ 's' => 'asdfjalsdfjalskdjfalsdfjkasdflaksjdflaksjdfl' ] );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 200 );
		$this->assertResponseData( $response, [ $repo->serialize() ] );
	}
}
