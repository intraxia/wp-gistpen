<?php
namespace Intraxia\Jaxion\Test\Model;

use Intraxia\Gistpen\Model\Language;
use Intraxia\Gistpen\Test\TestCase;
use Intraxia\Jaxion\Axolotl\EntityManager;
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
		$this->language = wp_insert_term( 'php', 'wpgp_language' );
	}

	public function test_repo_should_have_correct_properties() {
		/** @var Language $blob */
		$language = $this->database->find( 'Intraxia\Gistpen\Model\Language', $this->language['term_id'] );

		$this->assertSame( $this->language['term_id'], $language->ID );
		$this->assertSame( 'php', $language->slug );
	}
}
