<?php
namespace Intraxia\Gistpen\Test\Integration\Console\User;

use Intraxia\Gistpen\Test\Integration\Console\TestCase as BaseTestCase;
use Intraxia\Gistpen\Console\Command\User as UserCommand;
use Intraxia\Gistpen\Options\User as UserOptions;

abstract class TestCase extends BaseTestCase {
	/**
	 * @var UserCommand
	 */
	public $command;

	/**
	 * @var UserOptions
	 */
	public $options;

	public function setUp() {
		parent::setUp();

		$this->command = $this->app->make( UserCommand::class );
		$this->options = $this->app->make( UserOptions::class );
	}
}
