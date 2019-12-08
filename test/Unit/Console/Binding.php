<?php
namespace Intraxia\Gistpen\Test\Unit\Console;

use Intraxia\Gistpen\Test\Unit\TestCase;
use Intraxia\Gistpen\Console\Binding;

class BindingTest extends TestCase {

	/**
	 * @var Binding
	 */
	private $gist;

	public function setUp() {
		parent::setUp();

		$this->binding = $this->app->make( Binding::class );
	}

	public function test_registers_command() {
		$mock = \Mockery::mock( 'overload:WP_CLI' );
		$mock->shouldReceive( 'add_command' );

		$this->binding->register_command();
	}
}
