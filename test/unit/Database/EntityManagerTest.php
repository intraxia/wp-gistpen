<?php
namespace Intraxia\Jaxion\Test\Database;

use Intraxia\Gistpen\Database\EntityManager;
use Intraxia\Gistpen\Model\Blob;
use Intraxia\Gistpen\Model\Language;
use Intraxia\Gistpen\Model\Repo;
use Intraxia\Gistpen\Test\TestCase;

class EntityManagerTest extends TestCase {
	/**
	 * @var EntityManager
	 */
	protected $em;

	public function setUp() {
		parent::setUp();

		$this->create_post_and_children();
		$this->em = new EntityManager( 'wpgp' );
	}

	public function test_should_return_error_for_non_gistpen_class() {
		$this->assertInstanceOf( 'WP_Error', $this->em->find( 'WP_Post', 1 ) );
	}

	public function test_should_return_error_for_non_gistpen_repo() {
		$post_id = $this->factory->post->create();

		$this->assertInstanceOf( 'WP_Error', $this->em->find( EntityManager::REPO_CLASS, $post_id ) );
	}

	public function test_should_return_error_for_non_gistpen_blob() {
		$post_id = $this->factory->post->create();

		$this->assertInstanceOf( 'WP_Error', $this->em->find( EntityManager::BLOB_CLASS, $post_id ) );
	}

	public function test_should_return_error_for_non_language_term() {
		$term_id = $this->factory->term->create();

		$this->assertInstanceOf( 'WP_Error', $this->em->find( EntityManager::LANGUAGE_CLASS, $term_id ) );
	}

	public function test_should_return_full_repo() {
		/** @var Repo $model */
		$model = $this->em->find( EntityManager::REPO_CLASS, $this->repo->ID );

		$this->assertInstanceOf( EntityManager::REPO_CLASS, $model );
		$this->assertEquals( $this->repo, $model->get_underlying_wp_object() );
		$this->assertCount( 3, $model->blobs );
	}

	public function test_should_return_full_blob() {
		foreach ( $this->blobs as $blob ) {
			/** @var Blob $model */
			$model = $this->em->find( EntityManager::BLOB_CLASS, $blob );
			$this->assertInstanceOf( EntityManager::BLOB_CLASS, $model );
			$this->assertEquals( get_post( $blob ), $model->get_underlying_wp_object() );
			$this->assertInstanceOf( EntityManager::LANGUAGE_CLASS, $model->language );
			$this->assertSame( 'php', $model->language->slug );
		}
	}

	public function test_should_return_full_language() {
		/** @var Language $model */
		$model = $this->em->find( EntityManager::LANGUAGE_CLASS, $this->language->term_id );

		$this->assertInstanceOf( EntityManager::LANGUAGE_CLASS, $model );
		$this->assertSame( 'php', $model->slug );
	}
}
