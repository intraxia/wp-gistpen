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

	public function test_returns_error_with_invalid_blobs() {
		$this->set_role( 'administrator' );
		$request = new WP_REST_Request( 'POST', '/intraxia/v1/gistpen/repos' );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( 400, $response );
		$this->assertResponseData( array( 'message' => 'Missing parameter(s): blobs', ), $response );
	}
}
