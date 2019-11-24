<?php
namespace Intraxia\Gistpen\Test\Integration\Repo;

use WP_REST_Request;
use Intraxia\Gistpen\Model\Blob;
use Intraxia\Gistpen\Model\Language;
use Intraxia\Gistpen\Model\Repo;
use Intraxia\Gistpen\Test\Integration\TestCase;

class CollectionTest extends TestCase {
	public function test_returns_no_repos() {
		$request = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/repos' );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 200 );
		$this->assertResponseData( $response, [] );
	}

	public function test_returns_published_repo() {
		$repo    = $this->fm->create( Repo::class, [ 'status' => 'publish' ] );
		$request = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/repos' );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 200 );
		$this->assertResponseData( $response, [
			[
				'ID'          => $repo->ID,
				'description' => $repo->description,
				'slug'        => $repo->slug,
				'status'      => $repo->status,
				'password'    => $repo->password,
				'gist_id'     => $repo->gist_id,
				'gist_url'    => $repo->gist_url,
				'sync'        => $repo->sync,
				'blobs'       => $repo->blobs->serialize(),
				'rest_url'    => $repo->rest_url,
				'commits_url' => $repo->commits_url,
				'html_url'    => $repo->html_url,
				'created_at'  => $repo->created_at,
				'updated_at'  => $repo->updated_at,
			],
		] );
	}

	public function test_hide_unpublished_repo_in_db() {
		$repo    = $this->fm->create( Repo::class, [ 'status' => 'publish' ] );
		$request = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/repos' );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 200 );
		$this->assertResponseData( $response, [
			[
				'ID'          => $repo->ID,
				'description' => $repo->description,
				'slug'        => $repo->slug,
				'status'      => $repo->status,
				'password'    => $repo->password,
				'gist_id'     => $repo->gist_id,
				'gist_url'    => $repo->gist_url,
				'sync'        => $repo->sync,
				'blobs'       => $repo->blobs->serialize(),
				'rest_url'    => $repo->rest_url,
				'commits_url' => $repo->commits_url,
				'html_url'    => $repo->html_url,
				'created_at'  => $repo->created_at,
				'updated_at'  => $repo->updated_at,
			],
		] );
	}

	public function test_returns_attached_blobs() {
		$repo    = $this->fm->create( Repo::class, [ 'status' => 'publish' ] );
		$blob    = $this->fm->create( Blob::class, [
			'repo_id' => $repo->ID,
		] );
		$request = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/repos' );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 200 );
		$this->assertResponseData( $response, [
			[
				'ID'          => $repo->ID,
				'description' => $repo->description,
				'slug'        => $repo->slug,
				'status'      => $repo->status,
				'password'    => $repo->password,
				'gist_id'     => $repo->gist_id,
				'gist_url'    => $repo->gist_url,
				'sync'        => $repo->sync,
				'blobs'       => [
					[
						'ID'       => $blob->ID,
						'size'     => $blob->size,
						'raw_url'  => $blob->raw_url,
						'edit_url' => $blob->edit_url,
						'filename' => $blob->filename,
						'code'     => $blob->code,
						'language' => [
							'ID'           => $blob->language->ID,
							'display_name' => $blob->language->display_name,
							'slug'         => $blob->language->slug,
						],
					],
				],
				'rest_url'    => $repo->rest_url,
				'commits_url' => $repo->commits_url,
				'html_url'    => $repo->html_url,
				'created_at'  => $repo->created_at,
				'updated_at'  => $repo->updated_at,
			],
		] );
	}

	public function test_returns_error_invalid_page() {
		$request = WP_REST_Request::from_url( rest_url() . 'intraxia/v1/gistpen/repos?page=xyz' );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 400 );
		$this->assertResponseData( $response, [
			'code'    => 'rest_invalid_param',
			'message' => 'Invalid parameter(s): page',
			'data'    => [
				'status' => 400,
				'params' => [
					'page' => 'Param "page" is not a number, received xyz',
				],
			],
		] );
	}

	public function test_page_parameter() {
		$this->fm->seed( 15, Repo::class, [ 'status' => 'publish' ] );

		// Do a basic request.
		$request  = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/repos' );
		$response = $this->server->dispatch( $request );

		// Check we get the correct headers for the number of pages.
		$this->assertResponseStatus( $response, 200 );
		$this->assertCount( 10, $response->get_data() );
		$this->assertResponseHeader( $response, 'X-WP-Total', 15 );
		$this->assertResponseHeader( $response, 'X-WP-TotalPages', 2 );

		// Check we get the first 10 posts when page is 1.
		$request = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/repos' );
		$request->set_query_params( array(
			'page' => '1',
		) );

		$response = $this->server->dispatch( $request );
		$this->assertResponseStatus( $response, 200 );
		$this->assertCount( 10, $response->get_data() );

		// Check we get the next 5 posts when page is 2.
		$request = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/repos' );
		$request->set_query_params( array(
			'page' => '2',
		) );

		$response = $this->server->dispatch( $request );
		$this->assertResponseStatus( $response, 200 );
		$this->assertCount( 5, $response->get_data() );

		// Check we get no posts when page is 3.
		$request = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/repos' );
		$request->set_query_params( array(
			'page' => '3',
		) );

		$response = $this->server->dispatch( $request );
		$this->assertResponseStatus( $response, 200 );
		$this->assertCount( 0, $response->get_data() );
	}
}
