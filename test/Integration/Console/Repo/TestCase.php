<?php
namespace Intraxia\Gistpen\Test\Integration\Console\Repo;

use Intraxia\Gistpen\Test\Integration\Console\TestCase as BaseTestCase;
use Intraxia\Gistpen\Console\Command\Repo as RepoCommand;

abstract class TestCase extends BaseTestCase {
	/**
	 * @var Repo
	 */
	public $command;

	public function setUp() {
		parent::setUp();

		$this->command = $this->app->make( RepoCommand::class );
	}
}
