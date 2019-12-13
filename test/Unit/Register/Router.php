<?php
namespace Intraxia\Gistpen\Test\Unit\Register;

use Intraxia\Gistpen\Test\Unit\TestCase;
use Intraxia\Gistpen\Model\Repo;
use Intraxia\Gistpen\Register\Router as Register;
use Intraxia\Jaxion\Http\Router as CoreRouter;

class RouterTest extends TestCase {
	/**
	 * @var Register
	 */
	public $register;

	public function setUp() {
		parent::setUp();

		$this->register = $this->app->make( Register::class );
	}

	public function test_should_register_routes() {
		$this->register->add_routes( $this->app->get( CoreRouter::class ) );
	}
}
