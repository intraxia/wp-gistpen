<?php
namespace Intraxia\Gistpen\Test\Integration\Console\Site;

use Intraxia\Gistpen\Options\Site as SiteOptions;

class GetTest extends TestCase {
	/**
	 * Default associated arguments.
	 *
	 * @var array
	 */
	public static $assoc = [ 'format' => 'json' ];

	public function test_should_get_all_options() {
		$this->cli->shouldReceive( 'print_value' )
			->once()
			->withArgs( [ SiteOptions::$defaults, static::$assoc ] );

			$this->command->get( [], static::$assoc );
	}

	public function test_should_error_on_invald_key() {
		$this->cli->shouldReceive( 'error' )
			->once()
			->withArgs( [ '"invalid" is not a valid key.' ] );

		$this->command->get( [ 'invalid' ], static::$assoc );
	}

	public function test_should_print_valid_key() {
		$this->cli->shouldReceive( 'print_value' )
			->once()
			->withArgs( [ SiteOptions::$defaults['prism'], static::$assoc ] );

			$this->command->get( [ 'prism' ], static::$assoc );
	}

	public function test_should_error_on_invald_subkey() {
		$this->cli->shouldReceive( 'error' )
			->once()
			->withArgs( [ '"prism.invalid" is not a valid key.' ] );

		$this->command->get( [ 'prism.invalid' ], static::$assoc );
	}

	public function test_should_print_valid_key_and_subkey() {
		$this->cli->shouldReceive( 'print_value' )
			->once()
			->withArgs( [ SiteOptions::$defaults['prism']['theme'], static::$assoc ] );

			$this->command->get( [ 'prism.theme' ], static::$assoc );
	}
}
