<?php
namespace Intraxia\Gistpen\Test\Unit\View;

use Intraxia\Gistpen\Test\Unit\TestCase;
use Intraxia\Gistpen\View\Translations;

class TranslationsTest extends TestCase {

	private $translations;

	public function setUp() {
		parent::setUp();

		$this->translations = $this->app->make( Translations::class );
	}

	public function test_translate_returns_translation_correct_key() {
		$theme = $this->translations->translate( 'editor.theme' );

		$this->assertSame( 'Theme', $theme );
	}

	public function test_translate_returns_not_found_incorrect_key() {
		$result = $this->translations->translate( 'what.is.this' );

		$this->assertSame( 'Translation for key what.is.this not found.', $result );
	}

	public function test_serializes() {
		$this->assertArrayHasKey( 'i18n.notfound', $this->translations->serialize() );
	}

	public function test_translation_output() {
		\ob_start();
		$this->translations->output_translations();
		$output = \ob_get_clean();

		$this->assertRegexp( '/<script type="application\/javascript">/', $output );
		$this->assertRegexp( '/window.__GISTPEN_I18N__/', $output );
		$this->assertRegexp( '/<\/script>/', $output );
	}
}
