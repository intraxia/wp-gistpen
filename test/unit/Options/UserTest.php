<?php
namespace Intraxia\GIstpen\Test\Options;

use Intraxia\Gistpen\Options\User;
use WP_UnitTestCase;

class UserTest extends WP_UnitTestCase {
	/**
	 * @var User
	 */
	protected $user;

	public function setUp() {
		parent::setUp();
		$this->user = new User;

		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		update_user_meta( get_current_user_id(), '_wpgp_ace_theme', 'test' );
	}

	public function test_should_retrieve_all_users_options() {
		$this->assertSame( array( 'ace_theme' => 'test' ), $this->user->all() );
	}

	public function test_should_retrieve_ace_theme() {
		$this->assertSame( 'test', $this->user->get( 'ace_theme' ) );
	}

	public function test_should_update_ace_theme() {
		$value = 'newtest';

		$this->user->set( 'ace_theme', $value );

		$this->assertSame( $value, $this->user->get( 'ace_theme' ) );
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
