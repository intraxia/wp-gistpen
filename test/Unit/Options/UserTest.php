<?php
namespace Intraxia\Gistpen\Test\Unit\Options;

use Intraxia\Gistpen\Options\User;
use Intraxia\Gistpen\Test\Unit\TestCase;
use InvalidArgumentException;

class UserTest extends TestCase {
	/**
	 * @var User
	 */
	protected $user;

	protected $legacy = array(
		'ace_theme'      => 'test',
		'ace_invisibles' => 'on',
		'ace_tabs'       => 'off',
		'ace_width'      => '1',
	);

	protected $dummy = array(
		'editor' => array(
			'theme'              => 'twilight',
			'invisibles_enabled' => 'on',
			'tabs_enabled'       => 'on',
			'indent_width'       => '2',
		),
	);

	public function setUp() {
		parent::setUp();
		$this->user = new User();

		$this->user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		update_user_meta( $this->user_id, 'wpgp_options', $this->dummy );
	}

	public function test_should_retrieve_all_users_options() {
		$this->assertSame( $this->dummy, $this->user->all( $this->user_id ) );
	}

	public function test_should_retrieve_editor_theme() {
		$this->assertSame( $this->dummy['editor']['theme'], $this->user->get( $this->user_id, 'editor.theme' ) );
	}

	public function test_should_update_editor_theme() {
		$value = 'newtest';

		$this->user->set( $this->user_id, 'editor.theme', $value );

		$this->assertSame( $value, $this->user->get( $this->user_id, 'editor.theme' ) );
	}

	public function test_should_retrieve_editor_invisibles_enabled() {
		$this->assertSame( $this->dummy['editor']['invisibles_enabled'], $this->user->get( $this->user_id, 'editor.invisibles_enabled' ) );
	}

	public function test_should_update_editor_invisibles_enabled() {
		$value = 'off';

		$this->user->set( $this->user_id, 'editor.invisibles_enabled', $value );

		$this->assertSame( $value, $this->user->get( $this->user_id, 'editor.invisibles_enabled' ) );
	}

	public function test_should_retrieve_editor_tabs_enabled() {
		$this->assertSame( $this->dummy['editor']['tabs_enabled'], $this->user->get( $this->user_id, 'editor.tabs_enabled' ) );
	}

	public function test_should_update_editor_tabs_enabled() {
		$value = 'off';

		$this->user->set( $this->user_id, 'editor.tabs_enabled', $value );

		$this->assertSame( $value, $this->user->get( $this->user_id, 'editor.tabs_enabled' ) );
	}

	public function test_should_retrieve_editor_indent_width() {
		$this->assertSame( $this->dummy['editor']['indent_width'], $this->user->get( $this->user_id, 'editor.indent_width' ) );
	}

	public function test_should_update_editor_indent_width() {
		$value = '8';

		$this->user->set( $this->user_id, 'editor.indent_width', $value );

		$this->assertSame( $value, $this->user->get( $this->user_id, 'editor.indent_width' ) );
	}

	public function test_should_patch_editor() {
		$patch = array(
			'editor' => array(
				'indent_width' => '4',
				'tabs_enabled' => 'off',
			),
		);

		$this->user->patch( $this->user_id, $patch );

		$this->assertSame( $patch['editor']['indent_width'], $this->user->get( $this->user_id, 'editor.indent_width' ) );
		$this->assertSame( $patch['editor']['tabs_enabled'], $this->user->get( $this->user_id, 'editor.tabs_enabled' ) );
	}

	public function test_should_throw_exception_getting_unknown_option() {
		$this->expectException( InvalidArgumentException::class );

		$this->user->get( $this->user_id, 'unknown_option' );

		$this->expectException( InvalidArgumentException::class );

		$this->user->get( $this->user_id, 'editor.unknown_option' );
	}

	public function test_should_throw_exception_setting_unknown_option() {
		$this->expectException( InvalidArgumentException::class );

		$this->user->set( $this->user_id, 'unknown_option', 'test' );

		$this->expectException( InvalidArgumentException::class );

		$this->user->set( $this->user_id, 'editor.unknown_option', 'test' );
	}

	public function test_should_throw_exception_patching_unknown_option() {
		$this->expectException( InvalidArgumentException::class );

		$this->user->patch( $this->user_id, array( 'unknown_option' => 'test' ) );

		$this->expectException( InvalidArgumentException::class );

		$this->user->patch( $this->user_id, array( 'editor' => array( 'unknown_option' => 'test' ) ) );
	}

	public function test_should_fall_back_to_defaults() {
		delete_user_meta( $this->user_id, 'wpgp_options' );

		$this->assertEquals( $this->user->all( $this->user_id ), array(
			'editor' => array(
				'theme'              => 'default',
				'invisibles_enabled' => 'off',
				'tabs_enabled'       => 'off',
				'indent_width'       => '2',
			),
		) );
	}

	public function test_should_read_from_legacy_values() {
		delete_user_meta( $this->user_id, 'wpgp_options' );

		foreach ( $this->legacy as $key => $value ) {
			update_user_meta( $this->user_id, "_wpgp_{$key}", $value );
		}

		$this->assertSame( $this->user->all( $this->user_id ), array(
			'editor' => array(
				'theme'              => 'test',
				'invisibles_enabled' => 'on',
				'tabs_enabled'       => 'off',
				'indent_width'       => '1',
			),
		) );
	}
}
