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
		$this->assertInstanceOf( 'WP_Error', $this->em->find_by( 'WP_Post' ) );
		$this->assertInstanceOf( 'WP_Error', $this->em->create( 'WP_Post' ) );
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

	public function test_should_return_all_repos_in_collection() {
		$repos = $this->em->find_by( EntityManager::REPO_CLASS );

		$this->assertInstanceOf( 'Intraxia\Jaxion\Axolotl\Collection', $repos );
		$this->assertCount( 1, $repos );
	}

	public function test_should_return_all_blobs_in_collection() {
		$blobs = $this->em->find_by( EntityManager::BLOB_CLASS );

		$this->assertInstanceOf( 'Intraxia\Jaxion\Axolotl\Collection', $blobs );
		$this->assertCount( 3, $blobs );
	}

	public function test_should_return_all_languages_in_collection() {
		$languages = $this->em->find_by( EntityManager::LANGUAGE_CLASS );

		$this->assertInstanceOf( 'Intraxia\Jaxion\Axolotl\Collection', $languages );
		$this->assertCount( 1, $languages );
	}

	public function test_should_create_new_repo_with_blobs_and_languages() {
		$language = array(
			'slug' => 'php'
		);
		$blobs    = array(
			array(
				'filename' => 'new-file.txt',
				'code'     => 'Some code goes here',
			)
		);
		$repo     = array(
			'description' => 'New Repo',
			'status' => 'draft',
			'password' => '',
			'sync' => 'off',
		);

		$data = $repo;
		$data['blobs'] = $blobs;
		$data['blobs'][0]['language'] = $language;

		/** @var Repo $model */
		$model = $this->em->create( EntityManager::REPO_CLASS, $data );

		$this->assertInstanceOf( EntityManager::REPO_CLASS, $model );
		$this->assertEquals(
			get_post( $model->get_primary_id() ),
			$model->get_underlying_wp_object()
		);

		foreach ( $repo as $key =>$value ) {
			$this->assertSame( $value, $model->get_attribute( $key ) );
		}

		$this->assertInstanceOf( 'Intraxia\Jaxion\Axolotl\Collection', $model->blobs );
		$this->assertCount( 1, $model->blobs );

		$blob = $model->blobs->at( 0 );

		foreach ( $blobs[0] as $key => $value ) {
			$this->assertSame( $value, $blob->get_attribute( $key ) );
		}

		foreach ( $language as $key => $value ) {
			$this->assertSame( $value, $blob->language->get_attribute( $key ) );
		}
	}
}
