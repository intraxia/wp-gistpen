<?php
namespace Intraxia\Gistpen\Test\Integration\Console\User;

class GetTest extends TestCase {
	/**
	 * Default associated arguments.
	 *
	 * @var array
	 */
	public static $assoc = [ 'format' => 'json' ];

	public function test_should_get_all_user_by_email() {
		$user = $this->factory->user->create_and_get();
		$this->cli->shouldReceive( 'print_value' )
			->once()
			->withArgs( [ $this->options->all( $user->ID ), static::$assoc ] );

		$this->command->get( [ $user->user_email ], static::$assoc );
	}

	public function test_should_get_user_meta_by_path() {
		$user = $this->factory->user->create_and_get();
		$this->cli->shouldReceive( 'print_value' )
			->once()
			->withArgs( [
				$this->options->get( $user->ID, 'editor.indent_width' ),
				static::$assoc,
			] );

		$this->command->get( [ $user->user_email, 'editor.indent_width' ], static::$assoc );
	}
}
