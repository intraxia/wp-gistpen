<?php
namespace Intraxia\Gistpen\Test\Integration\Http\Search;

use WP_REST_Request;
use Intraxia\Gistpen\Model\Blob;
use Intraxia\Gistpen\Model\Repo;
use Intraxia\Gistpen\Test\Integration\TestCase;

class BlobsTest extends TestCase {
	public function setUp() {
		parent::setUp();

		$this->repo        = $this->fm->create( Repo::class, [ 'status' => 'publish' ] );
		$this->blob        = $this->fm->create( Blob::class, [ 'repo_id' => $this->repo->ID ] );
		$this->repo->blobs = $this->repo->blobs->add( $this->blob );

		$this->blob_output = [
			'ID'            => $this->blob->ID,
			'filename'      => $this->blob->filename,
			'code'          => $this->blob->code,
			'repo_id'       => $this->blob->repo_id,
			'language'      => $this->blob->language->serialize(),
			'rest_url'      => $this->blob->rest_url,
			'repo_rest_url' => $this->blob->repo_rest_url,
		];
	}

	public function test_should_return_error_on_extra_param() {
		$request = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/search/blobs' );
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

	public function test_should_return_blob_only_by_default() {
		$request = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/search/blobs' );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 200 );
		$this->assertResponseData( $response, [ $this->blob_output ] );
	}

	public function test_should_search_for_existing_blob() {
		$request = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/search/blobs' );
		$request->set_query_params( [ 's' => $this->blob->filename ] );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 200 );
		$this->assertResponseData( $response, [ $this->blob_output ] );
	}

	public function test_should_not_return_existing_blob_if_search_doesnt_match() {
		$request = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/search/blobs' );
		$request->set_query_params( [ 's' => 'asdfjalsdfjalskdjfalsdfjkasdflaksjdflaksjdfl' ] );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 200 );
		$this->assertResponseData( $response, [] );
	}
}
