<?php

/**
 * @group objects
 */
class WP_Gistpen_Language_Test extends WP_Gistpen_UnitTestCase {

	public $mock_term;

	public $language;

	function setUp() {
		parent::setUp();
	}

	function test_return_prism_slug() {
		$this->mock_lang
			->expects( $this->any() )
			->method( '__get' )
			->with( 'slug' )
			->will( $this->returnValue('slug') );
		$this->language = new WP_Gistpen_Language( $this->mock_lang );

		$this->assertEquals( 'slug', $this->language->prism_slug );
	}

	function test_fix_prism_slug_javascript() {
		$this->mock_lang
			->expects( $this->any() )
			->method( '__get' )
			->with( 'slug' )
			->will( $this->returnValue('js') );
		$this->language = new WP_Gistpen_Language( $this->mock_lang );

		$this->assertEquals( 'javascript', $this->language->prism_slug );
	}

	function test_fix_prism_slug_sass() {
		$this->mock_lang
			->expects( $this->any() )
			->method( '__get' )
			->with( 'slug' )
			->will( $this->returnValue('sass') );
		$this->language = new WP_Gistpen_Language( $this->mock_lang );

		$this->assertEquals( 'scss', $this->language->prism_slug );
	}

	function test_return_file_ext() {
		$this->mock_lang
			->expects( $this->any() )
			->method( '__get' )
			->with( 'slug' )
			->will( $this->returnValue('slug') );
		$this->language = new WP_Gistpen_Language( $this->mock_lang );

		$this->assertEquals( 'slug', $this->language->file_ext );
	}

	function test_fix_file_ext_bash() {
		$this->mock_lang
			->expects( $this->any() )
			->method( '__get' )
			->with( 'slug' )
			->will( $this->returnValue('bash') );
		$this->language = new WP_Gistpen_Language( $this->mock_lang );

		$this->assertEquals( 'sh', $this->language->file_ext );
	}

	function test_fix_file_ext_sass() {
		$this->mock_lang
			->expects( $this->any() )
			->method( '__get' )
			->with( 'slug' )
			->will( $this->returnValue('sass') );
		$this->language = new WP_Gistpen_Language( $this->mock_lang );

		$this->assertEquals( 'scss', $this->language->file_ext );
	}

	function test_return_display_name() {
		$this->mock_lang
			->expects( $this->any() )
			->method( '__get' )
			->with( 'name' )
			->will( $this->returnValue('Language name') );
		$this->language = new WP_Gistpen_Language( $this->mock_lang );

		$this->assertEquals( 'Language name', $this->language->display_name );
	}

	function tearDown() {
		parent::tearDown();
	}
}
