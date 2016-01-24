<?php
namespace Intraxia\Gistpen\Test\Options;

use Intraxia\Gistpen\Options\User;
use Intraxia\Gistpen\Test\TestCase;

class UserTest extends TestCase {
	/**
	 * @var User
	 */
	protected $user;

	protected $dummy = array(
		'ace_theme'      => 'test',
		'ace_invisibles' => 'on',
		'ace_tabs'       => 'off',
		'ace_width'      => '1',
	);

	public function setUp() {
		parent::setUp();
		$this->user = new User;

		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		foreach ( $this->dummy as $key => $value ) {
			update_user_meta( get_current_user_id(), "_wpgp_{$key}", $value );
		}
	}

	public function test_should_retrieve_all_users_options() {
		$this->assertSame( $this->dummy, $this->user->all() );
	}

	public function test_should_retrieve_ace_theme() {
		$this->assertSame( $this->dummy['ace_theme'], $this->user->get( 'ace_theme' ) );
	}

	public function test_should_update_ace_theme() {
		$value = 'newtest';

		$this->user->set( 'ace_theme', $value );

		$this->assertSame( $value, $this->user->get( 'ace_theme' ) );
	}

	public function test_should_retrieve_ace_tabs() {
		$this->assertSame( $this->dummy['ace_tabs'], $this->user->get( 'ace_tabs' ) );
	}

	public function test_should_update_ace_tabs() {
		$value = 'on';

		$this->user->set( 'ace_tabs', $value );

		$this->assertSame( $value, $this->user->get( 'ace_tabs' ) );
	}

	public function test_should_retrieve_ace_invisibles() {
		$this->assertSame( $this->dummy['ace_invisibles'], $this->user->get( 'ace_invisibles' ) );
	}

	public function test_should_update_ace_invisibles() {
		$value = 'off';

		$this->user->set( 'ace_invisibles', $value );

		$this->assertSame( $value, $this->user->get( 'ace_invisibles' ) );
	}

	public function test_should_retrieve_ace_width() {
		$this->assertSame( $this->dummy['ace_width'], $this->user->get( 'ace_width' ) );
	}

	public function test_should_update_ace_width() {
		$value = '8';

		$this->user->set( 'ace_width', $value );

		$this->assertSame( $value, $this->user->get( 'ace_width' ) );
	}

	public function test_should_throw_exception_getting_unknown_option() {
		$this->setExpectedException( 'InvalidArgumentException' );

		$this->user->get( 'unknown_option' );
	}

	public function test_should_throw_exception_setting_unknown_option() {
		$this->setExpectedException( 'InvalidArgumentException' );

		$this->user->set( 'unknown_option', 'test' );
	}
}
