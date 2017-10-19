<?php

class RepoCollectionTest extends ApiTestCase {
	public function test_returns_no_repos() {
		$request = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/repos' );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( 200, $response );
		$this->assertResponseData( array(), $response );
	}

	public function test_returns_repo_with_links() {
		$this->create_post_and_children( true );
		$request = new WP_REST_Request( 'GET', '/intraxia/v1/gistpen/repos' );

		$response = $this->server->dispatch( $request );
		/** @var \Intraxia\Gistpen\Model\Repo $repo */
		$repo     = $this->em->find( \Intraxia\Gistpen\Model\Klass::REPO, $this->repo->ID );

		$this->assertResponseStatus( 200, $response );
		$this->assertResponseData( array(
			array_merge( $repo->serialize(), array( '_links' => array(
				'blobs' => array(
					array(
						'href'       => rest_url('intraxia/v1/gistpen/repos/' . $repo->ID . '/blobs' ),
						'embeddable' => true,
					)
				)
			) ) )
		), $response );
	}
}
