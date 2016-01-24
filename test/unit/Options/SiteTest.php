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
		'gist_token'                => 'token_value',
		'gistpen_highlighter_theme' => 'highlighter_theme',
		'gistpen_line_number'       => 'on',
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
		$this->assertSame( $this->dummy['gistpen_highlighter_theme'], $this->site->get( 'gistpen_highlighter_theme' ) );
	}

	public function test_should_update_gistpen_highlighter_theme() {
		$value = 'newtest';

		$this->site->set( 'gistpen_highlighter_theme', $value );

		$this->assertSame( $value, $this->site->get( 'gistpen_highlighter_theme' ) );
	}

	public function test_should_retrieve_gistpen_line_number() {
		$this->assertSame( $this->dummy['gistpen_line_number'], $this->site->get( 'gistpen_line_number' ) );
	}

	public function test_should_update_gistpen_line_number() {
		$value = 'newtest';

		$this->site->set( 'gistpen_line_number', $value );

		$this->assertSame( $value, $this->site->get( 'gistpen_line_number' ) );
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
