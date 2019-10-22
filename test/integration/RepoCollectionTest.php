<?php

class RepoCollectionTest extends ApiTestCase {
	public function test_returns_no_repos() {
		$request = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/repos' );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( 200, $response );
		$this->assertResponseData( array(), $response );
	}

	public function test_returns_repo_in_db() {
		$this->create_post_and_children( true );
		$request = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/repos' );

		$response = $this->server->dispatch( $request );
		/** @var \Intraxia\Gistpen\Database\EntityManager $em */
		$em       = $this->app->fetch( 'database' );
		/** @var \Intraxia\Gistpen\Model\Repo $repo */
		$repo     = $em->find( \Intraxia\Gistpen\Model\Klass::REPO, $this->repo->ID, array(
			'with' => array(
				'blobs' => array(
					'with' => 'language'
				)
			)
		) );

		$this->assertResponseStatus( 200, $response );
		$this->assertResponseData( array( $repo->serialize() ), $response );
		// @TODO(mAAdhaTTah) spell out expected response
		$this->assertEquals( $repo->slug, $response->get_data()[0]['slug'] );
	}

	public function test_returns_error_invalid_page() {
		$this->create_post_and_children( true );
		$request = WP_REST_Request::from_url( rest_url() . 'intraxia/v1/gistpen/repos?page=xyz' );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( 400, $response );
		$this->assertResponseData( array( 'message' => 'Invalid parameter(s): page', ), $response );
	}

	public function test_page_parameter() {
		$count = 0;

		// Create 15 Repos in the db.
		while ($count < 15) {
			$this->create_post_and_children( true );
			$count++;
		}

		// Do a basic request.
		$request = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/repos' );
		$response = $this->server->dispatch( $request );

		// Check we get the correct headers for the number of pages.
		$this->assertResponseStatus( 200, $response );
		$this->assertCount( 10, $response->get_data() );
		$this->assertResponseHeader( 'X-WP-Total', 15 , $response );
		$this->assertResponseHeader( 'X-WP-TotalPages', 2 , $response );

		// Check we get the first 10 posts when page is 1.
		$request = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/repos' );
		$request->set_query_params( array(
			'page' => '1',
		) );

		$response = $this->server->dispatch( $request );
		$this->assertResponseStatus( 200, $response );
		$this->assertCount( 10, $response->get_data() );

		// Check we get the next 5 posts when page is 2.
		$request = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/repos' );
		$request->set_query_params( array(
			'page' => '2',
		) );

		$response = $this->server->dispatch( $request );
		$this->assertResponseStatus( 200, $response );
		$this->assertCount( 5, $response->get_data() );

		// Check we get no posts when page is 3.
		$request = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/repos' );
		$request->set_query_params( array(
			'page' => '3',
		) );

		$response = $this->server->dispatch( $request );
		$this->assertResponseStatus( 200, $response );
		$this->assertCount( 0, $response->get_data() );
	}

	public function test_returns_error_with_invalid_blobs() {
		$this->set_role( 'administrator' );
		$request = new WP_REST_Request( 'POST', '/intraxia/v1/gistpen/repos' );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( 400, $response );
		$this->assertResponseData( array( 'message' => 'Missing parameter(s): blobs', ), $response );
	}
}
