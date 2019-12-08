<?php
namespace Intraxia\Gistpen\Test\Unit\Options;

use Intraxia\Gistpen\Options\Site;
use Intraxia\Gistpen\Test\Unit\TestCase;

class SiteTest extends TestCase {
	/**
	 * @var Site
	 */
	protected $site;

	/**
	 * @var string
	 */
	protected $slug;

	public function setUp() {
		parent::setUp();
		$this->site = $this->app->make( Site::class );
		$this->slug = $this->app->get( 'slug' );
	}

	public function tearDown() {
		parent::tearDown();

		delete_option( $this->slug . '_no_priv' );
		delete_option( $this->slug . '_priv' );
	}

	public function test_should_retrieve_site_options() {
		$this->assertSame( Site::$defaults, $this->site->all() );
	}

	public function test_should_reset_mangled_options() {
		update_option( $this->slug . '_no_priv', 'something went wrong' );

		$this->assertSame( Site::$defaults, $this->site->all() );

		update_option( $this->slug . '_no_priv', array() );

		$this->assertSame( Site::$defaults, $this->site->all() );

		update_option( $this->slug . '_no_priv', false );

		$this->assertSame( Site::$defaults, $this->site->all() );

		update_option( $this->slug . '_no_priv', [
			'prism' => [
				'line-numbers'    => 'off',
				'show-invisibles' => 'on',
				'theme'           => 'invalid-theme',
			],
		] );

		$this->assertSame( $this->site->all(), [
			'prism' => [
				'line-numbers'    => false,
				'show-invisibles' => true,
				'theme'           => 'default',
			],
			'gist'  => Site::$defaults['gist'],
		] );
	}

	public function test_should_get_option() {
		$prism = $this->site->get( 'prism' );

		$this->assertSame( Site::$defaults['prism'], $prism );

		$gist = $this->site->get( 'gist' );

		$this->assertSame( Site::$defaults['gist'], $gist );
	}

	public function test_should_throw_on_invalid_key() {
		$this->expectException( \InvalidArgumentException::class );

		$this->site->get( 'random' );
	}

	public function test_should_throw_on_invalid_prism_key() {
		$this->expectException( \InvalidArgumentException::class );

		$this->site->patch( [ 'prism' => [ 'invalid_prop' => false ] ] );
	}

	public function test_should_throw_on_invalid_prism_theme_type() {
		$this->expectException( \InvalidArgumentException::class );

		$this->site->patch( [ 'prism' => [ 'theme' => false ] ] );
	}

	public function test_should_throw_on_invalid_prism_theme_name() {
		$this->expectException( \InvalidArgumentException::class );

		$this->site->patch( [ 'prism' => [ 'theme' => 'not-a-real-theme' ] ] );
	}

	public function test_should_update_valid_prism_theme() {
		$this->site->patch( [ 'prism' => [ 'theme' => 'xonokai' ] ] );

		$site = $this->site->all();
		$this->assertSame( 'xonokai', $site['prism']['theme'] );
	}

	public function test_should_throw_on_invalid_prism_ln_type() {
		$this->expectException( \InvalidArgumentException::class );

		$this->site->patch( [ 'prism' => [ 'line-numbers' => 123 ] ] );
	}

	public function test_should_throw_on_invalid_prism_si_type() {
		$this->expectException( \InvalidArgumentException::class );

		$this->site->patch( [ 'prism' => [ 'show-invisibles' => 123 ] ] );
	}

	public function test_should_update_boolean_prism_key() {
		$this->site->patch( array( 'prism' => array( 'line-numbers' => true ) ) );

		$site = $this->site->all();
		$this->assertSame( true, $site['prism']['line-numbers'] );

		$this->site->patch( array( 'prism' => array( 'line-numbers' => false ) ) );

		$site = $this->site->all();
		$this->assertSame( false, $site['prism']['line-numbers'] );
	}

	public function test_should_update_boolean_prism_key_with_string() {
		$this->site->patch( array( 'prism' => array( 'line-numbers' => 'on' ) ) );

		$site = $this->site->all();
		$this->assertSame( true, $site['prism']['line-numbers'] );

		$this->site->patch( array( 'prism' => array( 'line-numbers' => 'off' ) ) );

		$site = $this->site->all();
		$this->assertSame( false, $site['prism']['line-numbers'] );
	}

	public function test_should_throw_on_invalid_gist_token_type() {
		$this->expectException( \InvalidArgumentException::class );

		$this->site->patch( [ 'gist' => [ 'token' => 123 ] ] );
	}

	public function test_should_update_valid_gist_key() {
		$this->site->patch( array( 'gist' => array( 'token' => '123456789asghskdjfhka' ) ) );

		$site = $this->site->all();
		$this->assertSame( '123456789asghskdjfhka', $site['gist']['token'] );
	}
}
