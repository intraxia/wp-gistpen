<?php
namespace Intraxia\Gistpen\Test\Integration\Http\Blob;

use WP_REST_Request;
use Intraxia\Gistpen\Model\Blob;
use Intraxia\Gistpen\Model\Language;
use Intraxia\Gistpen\Model\Repo;
use Intraxia\Gistpen\Test\Integration\TestCase;

class ViewTest extends TestCase {

	/** @var Repo */
	public $repo;

	/** @var Blob */
	public $blob;

	public function setUp() {
		parent::setUp();
		$this->repo = $this->fm->create( Repo::class );
		$this->blob = $this->fm->create( Blob::class, [ 'repo_id' => $this->repo->ID ] );
	}

	public function test_should_404_on_invalid_repo_id() {
		$request = new WP_REST_Request( 'GET', "/intraxia/v1/gistpen/repos/1234/blobs/{$this->blob->ID}" );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 404 );
	}

	public function test_should_404_on_invalid_blob_id() {
		$request = new WP_REST_Request( 'GET', "/intraxia/v1/gistpen/repos/{$this->repo->ID}/blobs/1234" );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 404 );
	}

	public function test_should_return_blob() {
		$request = new WP_REST_Request( 'GET', "/intraxia/v1/gistpen/repos/{$this->repo->ID}/blobs/{$this->blob->ID}" );

		$response = $this->server->dispatch( $request );

		$this->assertResponseStatus( $response, 200 );
		$this->assertResponseData( $response, [
			'ID'       => $this->blob->ID,
			'size'     => $this->blob->size,
			'raw_url'  => $this->blob->raw_url,
			'edit_url' => $this->blob->edit_url,
			'filename' => $this->blob->filename,
			'code'     => $this->blob->code,
			'language' => [
				'ID'           => $this->blob->language->ID,
				'display_name' => $this->blob->language->display_name,
				'slug'         => $this->blob->language->slug,
			],
		] );
	}
}
