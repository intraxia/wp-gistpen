<?php
namespace Intraxia\Gistpen\Test\Model;

use Intraxia\Gistpen\Database\EntityManager;
use Intraxia\Gistpen\Model\Language;
use Intraxia\Gistpen\Test\TestCase;
use WP_Query;

class LanguageTest extends TestCase {
	/**
	 * @var EntityManager
	 */
	protected $database;

	/**
	 * @var int
	 */
	protected $language;

	public function setUp() {
		parent::setUp();

		$this->database = new EntityManager( new WP_Query, 'wpgp' );
		$this->language = wp_insert_term( 'js', 'wpgp_language' );
	}

	public function test_repo_should_have_correct_properties() {
		/** @var Language $language */
		$language = $this->database->find( EntityManager::LANGUAGE_CLASS, $this->language['term_id'] );

		$this->assertSame( $this->language['term_id'], $language->ID );
		$this->assertSame( 'js', $language->slug );
		$this->assertSame( 'javascript', $language->prism_slug );
		$this->assertSame( 'js', $language->file_ext );
		$this->assertSame( 'JavaScript', $language->display_name );
	}
}
