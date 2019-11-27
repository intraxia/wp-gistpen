<?php
namespace Intraxia\Gistpen\Test\Integration\Console\Blob;

use Intraxia\Gistpen\Test\Integration\Console\TestCase as BaseTestCase;
use Intraxia\Gistpen\Console\Command\Blob as BlobCommand;

abstract class TestCase extends BaseTestCase {
	/**
	 * @var BlobCommand
	 */
	public $command;

	public function setUp() {
		parent::setUp();

		$this->command = $this->app->make( BlobCommand::class );
	}
}
