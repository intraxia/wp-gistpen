<?php
namespace Intraxia\Jaxion\Test\Model;

use Intraxia\Gistpen\Model\Blob;
use Intraxia\Gistpen\Test\TestCase;
use Intraxia\Jaxion\Axolotl\EntityManager;
use WP_Post;
use WP_Query;

class BlobTest extends TestCase {
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
		$this->database = new EntityManager( new WP_Query, 'wpgp' );

		wp_set_post_terms( $this->blob->ID, 'php', 'wpgp_language' );
	}

	public function test_repo_should_have_correct_properties() {
		/** @var Blob $blob */
		$blob = $this->database->find( 'Intraxia\Gistpen\Model\Blob', $this->blob->ID );

		$this->assertSame( $this->blob->ID, $blob->ID );
		$this->assertSame( $this->blob->post_title, $blob->filename );
		$this->assertSame( $this->blob->post_content, $blob->code );
		$this->assertInstanceOf( 'Intraxia\Gistpen\Model\Language', $blob->language );
		$this->assertSame( strlen( $this->blob->post_content ), $blob->size );
		$this->assertSame( rest_url( sprintf(
			'intraxia/v1/gistpen/repos/%s/%s/%s',
			$this->repo->ID,
			$this->blob->ID,
			$blob->filename
		) ), $blob->raw_url );
		$this->assertInstanceOf( 'Intraxia\Gistpen\Model\Repo', $blob->repo );

		$json = $blob->serialize();

		$this->assertSame( 'php', $json['language'] );
	}
}
