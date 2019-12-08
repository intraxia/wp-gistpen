<?php
namespace Intraxia\Gistpen\Test\Integration\Console\User;

use Intraxia\Gistpen\Options\Site as SiteOptions;

class PatchTest extends TestCase {
	/**
	 * Default associated arguments.
	 *
	 * @var array
	 */
	public static $assoc = [ 'format' => 'json' ];

	public function test_should_patch_subkey_dot_separated() {
		$user = $this->factory->user->create_and_get();
		$args = [ 'editor.invisibles_enabled', 'on' ];
		$this->cli->shouldReceive( 'get_value_from_arg_or_stdin' )
			->once()
			->withArgs( [ $args, 1 ] )
			->andReturn( $args[1] );
		$this->cli->shouldReceive( 'read_value' )
			->once()
			->withArgs( [ $args[1], [] ] )
			->andReturn( $args[1] );
		$this->cli->shouldReceive( 'success' )
			->once()
			->withArgs( [ 'Updated user options.' ] );

		$this->command->patch( array_merge( [ $user->ID ], $args ), [] );

		$this->assertSame( $this->options->all( $user->ID ), [
			'editor' => [
				'theme'              => 'default',
				'invisibles_enabled' => 'on',
				'tabs_enabled'       => 'off',
				'indent_width'       => '2',
			],
		] );
	}
}
