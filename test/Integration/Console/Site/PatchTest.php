<?php
namespace Intraxia\Gistpen\Test\Integration\Console\Site;

use Intraxia\Gistpen\Options\Site as SiteOptions;

class PatchTest extends TestCase {
	/**
	 * Default associated arguments.
	 *
	 * @var array
	 */
	public static $assoc = [ 'format' => 'json' ];

	public function test_should_patch_from_stdin_if_no_args_provided() {
		$patch = [
			'prism' => [
				'theme' => 'twilight',
			],
		];
		$json  = wp_json_encode( $patch );
		$this->cli->shouldReceive( 'get_value_from_arg_or_stdin' )
			->once()
			->withArgs( [ [], -1 ] )
			->andReturn( $json );
		$this->cli->shouldReceive( 'read_value' )
			->once()
			->withArgs( [ $json, static::$assoc ] )
			->andReturn( $patch );
		$this->cli->shouldReceive( 'success' )
			->once()
			->withArgs( [ 'Updated site options.' ] );

		$this->command->patch( [], static::$assoc );

		$this->assertSame( $this->options->all(), [
			'prism' => [
				'theme'           => 'twilight',
				'line-numbers'    => false,
				'show-invisibles' => false,
			],
			'gist'  => SiteOptions::$defaults['gist'],
		] );
	}

	public function test_should_patch_root_if_value_provided() {
		$patch = [
			'prism' => [
				'theme' => 'twilight',
			],
		];
		$json  = wp_json_encode( $patch );
		$this->cli->shouldReceive( 'read_value' )
			->once()
			->withArgs( [ $json, static::$assoc ] )
			->andReturn( $patch );
		$this->cli->shouldReceive( 'success' )
			->once()
			->withArgs( [ 'Updated site options.' ] );

		$this->command->patch( [ $json ], static::$assoc );

		$this->assertSame( $this->options->all(), [
			'prism' => [
				'theme'           => 'twilight',
				'line-numbers'    => false,
				'show-invisibles' => false,
			],
			'gist'  => SiteOptions::$defaults['gist'],
		] );
	}

	public function test_should_patch_from_stdin_if_key_but_no_value_provided() {
		$patch = [ 'theme' => 'twilight' ];
		$json  = wp_json_encode( $patch );
		$this->cli->shouldReceive( 'get_value_from_arg_or_stdin' )
			->once()
			->withArgs( [ [ 'prism' ], 1 ] )
			->andReturn( $json );
		$this->cli->shouldReceive( 'read_value' )
			->once()
			->withArgs( [ $json, static::$assoc ] )
			->andReturn( $patch );
		$this->cli->shouldReceive( 'success' )
			->once()
			->withArgs( [ 'Updated site options.' ] );

		$this->command->patch( [ 'prism' ], static::$assoc );

		$this->assertSame( $this->options->all(), [
			'prism' => [
				'theme'           => 'twilight',
				'line-numbers'    => false,
				'show-invisibles' => false,
			],
			'gist'  => SiteOptions::$defaults['gist'],
		] );
	}

	public function test_should_patch_key_and_value() {
		$patch = [ 'theme' => 'twilight' ];
		$json  = wp_json_encode( $patch );
		$this->cli->shouldReceive( 'get_value_from_arg_or_stdin' )
			->once()
			->withArgs( [ [ 'prism', $json ], 1 ] )
			->andReturn( $json );
		$this->cli->shouldReceive( 'read_value' )
			->once()
			->withArgs( [ $json, static::$assoc ] )
			->andReturn( $patch );
		$this->cli->shouldReceive( 'success' )
			->once()
			->withArgs( [ 'Updated site options.' ] );

		$this->command->patch( [ 'prism', $json ], static::$assoc );

		$this->assertSame( $this->options->all(), [
			'prism' => [
				'theme'           => 'twilight',
				'line-numbers'    => false,
				'show-invisibles' => false,
			],
			'gist'  => SiteOptions::$defaults['gist'],
		] );
	}

	public function test_should_patch_subkey_dot_separated() {
		$args = [ 'prism.theme', 'twilight' ];
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
			->withArgs( [ 'Updated site options.' ] );

		$this->command->patch( $args, [] );

		$this->assertSame( $this->options->all(), [
			'prism' => [
				'theme'           => 'twilight',
				'line-numbers'    => false,
				'show-invisibles' => false,
			],
			'gist'  => SiteOptions::$defaults['gist'],
		] );
	}
}
