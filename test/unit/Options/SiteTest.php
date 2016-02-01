<?php
namespace Intraxia\Gistpen\Test\Options;

use Intraxia\Gistpen\Options\Site;
use WP_UnitTestCase;

class SiteTest extends WP_UnitTestCase {
	/**
	 * @var Site
	 */
	protected $site;

	/**
	 * @var array
	 */
	protected $dummy = array(
		'prism_theme'   => 'default',
		'prism_plugins' => array( 'line-numbers' ),
		'gist_token'    => 'token_value',
	);

	public function setUp() {
		parent::setUp();
		$this->site = new Site;

		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		foreach ( $this->dummy as $key => $value ) {
			cmb2_update_option( 'wp-gistpen', "_wpgp_{$key}", $value );
		}
	}

	public function test_should_retrieve_all_users_options() {
		$this->assertSame( $this->dummy, $this->site->all() );
	}

	public function test_should_retrieve_gist_token() {
		$this->assertSame( $this->dummy['gist_token'], $this->site->get( 'gist_token' ) );
	}

	public function test_should_update_gist_token() {
		$value = 'newtest';

		$this->site->set( 'gist_token', $value );

		$this->assertSame( $value, $this->site->get( 'gist_token' ) );
	}

	public function test_should_retrieve_gistpen_highlighter_theme() {
		$this->assertSame( $this->dummy['prism_theme'], $this->site->get( 'prism_theme' ) );
	}

	public function test_should_update_gistpen_highlighter_theme() {
		$value = 'newtest';

		$this->site->set( 'prism_theme', $value );

		$this->assertSame( $value, $this->site->get( 'prism_theme' ) );
	}

	public function test_should_retrieve_gistpen_line_number() {
		$this->assertSame( $this->dummy['prism_plugins'], $this->site->get( 'prism_plugins' ) );
	}

	public function test_should_update_gistpen_line_number() {
		$value = array( 'line-numbers', 'show-invisibles' );

		$this->site->set( 'prism_plugins', $value );

		$this->assertSame( $value, $this->site->get( 'prism_plugins' ) );
	}

	public function test_should_throw_exception_getting_unknown_option() {
		$this->setExpectedException( 'InvalidArgumentException' );

		$this->site->get( 'unknown_option' );
	}

	public function test_should_throw_exception_setting_unknown_option() {
		$this->setExpectedException( 'InvalidArgumentException' );

		$this->site->set( 'unknown_option', 'test' );
	}
}
