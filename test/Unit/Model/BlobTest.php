<?php
namespace Intraxia\Gistpen\Test\Unit\Model;

use Intraxia\Gistpen\Model\Blob;
use Intraxia\Gistpen\Model\Language;
use Intraxia\Gistpen\Test\Unit\TestCase;
use Intraxia\Jaxion\Contract\Axolotl\EntityManager;
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
		$this->database = $this->app->make( EntityManager::class );

		wp_set_post_terms( $this->blob->ID, 'php', 'wpgp_language' );
	}

	public function test_repo_should_have_correct_properties() {
		/** @var Blob $blob */
		$blob = $this->database->find( Blob::class, $this->blob->ID, array(
			'with' => 'language',
		) );

		$this->assertInstanceOf( Blob::class, $blob );
		$this->assertSame( $this->blob->ID, $blob->ID );
		$this->assertSame( $this->blob->post_title, $blob->filename );
		$this->assertSame( $this->blob->post_content, $blob->code );
		$this->assertInstanceOf( Language::class, $blob->language );
		$this->assertSame( strlen( $this->blob->post_content ), $blob->size );
		$this->assertSame( $this->repo->ID, $blob->repo_id );
		$this->assertSame( rest_url( sprintf(
			'intraxia/v1/gistpen/repos/%s/blobs/%s/raw',
			$this->repo->ID,
			$this->blob->ID
		) ), $blob->raw_url );

		$json = $blob->serialize();

		$this->assertSame( 'php', $json['language']['slug'] );
	}
}
