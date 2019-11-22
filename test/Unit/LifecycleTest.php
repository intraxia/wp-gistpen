<?php
namespace Intraxia\Gistpen\Test\Unit;

use Intraxia\Gistpen\Lifecycle;

class LifecycleTest extends TestCase {
	public function test_should_update_version_on_activation() {
		$lifecyle = $this->app->make( Lifecycle::class );

		$lifecyle->activate();

		$this->assertEquals( $this->app->get( 'version' ), get_option( 'wp_gistpen_version' ) );
		$this->assertEquals( 'done', get_option( '_wpgp_activated' ) );
	}

	public function test_should_flush_on_dectivation() {
		$lifecyle = $this->app->make( Lifecycle::class );

		$lifecyle->deactivate();
	}
}
