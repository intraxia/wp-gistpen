<?php
namespace Intraxia\Gistpen\Test\Unit\Client;

use Intraxia\Gistpen\Test\Unit\TestCase;
use Intraxia\Gistpen\Client\Gist;

class GistTest extends TestCase {

	/**
	 * @var Gist
	 */
	private $gist;

	public function setUp() {
		parent::setUp();

		$this->gist = $this->app->make( Gist::class );
	}

	public function test_all_returns_error_with_no_token() {
		$response = $this->gist->all();

		$this->assertWPError( $response );
	}
}
