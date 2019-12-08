<?php
namespace Intraxia\Gistpen\Test\Integration\Console\Site;

use Intraxia\Gistpen\Test\Integration\Console\TestCase as BaseTestCase;
use Intraxia\Gistpen\Console\Command\Site as SiteCommand;
use Intraxia\Gistpen\Options\Site as SiteOptions;

abstract class TestCase extends BaseTestCase {
	/**
	 * @var Site
	 */
	public $command;

	public function setUp() {
		parent::setUp();

		$this->command = $this->app->make( SiteCommand::class );
		$this->options = $this->app->make( SiteOptions::class );
	}
}
