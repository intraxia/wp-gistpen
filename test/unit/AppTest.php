<?php
namespace Intraxia\Gistpen\Test;

use Intraxia\Gistpen\App;
use PHPUnit_Framework_TestCase;

class AppTest extends PHPUnit_Framework_TestCase {
	public function test_should_update_version_on_activation() {
		$app = App::instance();

		$app->activate();

		$this->assertEquals( App::VERSION, get_option( 'wp_gistpen_version' ) );
	}

	public function test_should_update_activated_on_activation() {
		$app = App::instance();

		$app->activate();

		$this->assertEquals( 'done', get_option( '_wpgp_activated' ) );
	}
}
