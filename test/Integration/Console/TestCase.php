<?php
namespace Intraxia\Gistpen\Test\Integration\Console;

use Intraxia\Gistpen\Test\TestCase as BaseTestCase;
use Intraxia\Jaxion\Contract\Axolotl\EntityManager as EM;
use Intraxia\Gistpen\Database\EntityManager as DB;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use Mockery;

abstract class TestCase extends BaseTestCase {
	/**
	 * @var \Mockery\MockInterface
	 */
	protected $cli;

	/**
	 * @var EM
	 */
	public $em;

	public function setUp() {
		parent::setUp();

		require_once dirname( dirname( dirname( __DIR__ ) ) ) . '/lib/wp-cli/wp-cli/php/utils.php';

		$this->cli = Mockery::mock( 'alias:WP_CLI' );
		$this->em  = $this->app->make( EM::class );
	}

	public function tearDown() {
		parent::tearDown();

		// Rebind the db to the interface, overwriting any one-off mocks.
		$this->app->set( EM::class, $this->em );
	}
}
