<?php
namespace Intraxia\Gistpen\Test\Model;

use Intraxia\Gistpen\Database\EntityManager;
use Intraxia\Gistpen\Model\Repo;
use Intraxia\Gistpen\Test\TestCase;
use WP_Post;
use WP_Query;

class RepoTest extends TestCase {
	/**
	 * @var WP_Post
	 */
	protected $repo;

	/**
	 * @var WP_Post
	 */
	protected $blob;

	/**
	 * @var EntityManager
	 */
	protected $database;

	public function setUp() {
		parent::setUp();

		$this->repo     = $this->factory->gistpen->create_and_get();
		$this->blob     = $this->factory->gistpen->create_and_get( array( 'post_parent' => $this->repo->ID ) );
		$this->database = new EntityManager( 'wpgp' );

		update_post_meta( $this->repo->ID, '_wpgp_gist_id', 'this_gist_id' );
		update_post_meta( $this->repo->ID, '_wpgp_sync', 'off' );
	}

	public function test_repo_should_have_correct_properties() {
		/** @var Repo $repo */
		$repo = $this->database->find( 'Intraxia\Gistpen\Model\Repo', $this->repo->ID );

		$this->assertSame( $this->repo->ID, $repo->ID );
		$this->assertSame( $this->repo->post_title, $repo->description );
		$this->assertSame( $this->repo->post_password, $repo->password );
		$this->assertSame( $this->repo->post_status, $repo->status );
		$this->assertSame( $this->repo->post_date, $repo->created_at );
		$this->assertSame( $this->repo->post_modified, $repo->updated_at );
		$this->assertSame( 'this_gist_id', $repo->gist_id );
		$this->assertSame( 'off', $repo->sync );
		$this->assertInstanceOf( 'Intraxia\Jaxion\Axolotl\Collection', $repo->blobs );
		$this->assertCount( 1, $repo->blobs );
		$this->assertSame( rest_url( sprintf(
			'intraxia/v1/gistpen/repos/%s',
			$this->repo->ID
		) ), $repo->rest_url );
		$this->assertSame( rest_url( sprintf(
			'intraxia/v1/gistpen/repos/%s/commits',
			$this->repo->ID
		) ), $repo->commits_url );
		$this->assertSame( get_permalink( $this->repo->ID ), $repo->html_url );
	}
}
