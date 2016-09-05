<?php
namespace Intraxia\Gistpen\Test;

use Intraxia\Gistpen\App;
use Intraxia\Jaxion\Core\UndefinedAliasException;
use Mockery;
use WP_UnitTestCase;

abstract class TestCase extends WP_UnitTestCase {
	/**
	 * @var Factory
	 */
	protected $factory;

	/**
	 * @var App
	 */
	protected $app;

	public function setUp() {
		parent::setUp();
		$this->factory = new Factory;
		$this->app     = App::instance();
	}

	public function tearDown() {
		parent::tearDown();

		Mockery::close();
		cmb2_update_option( 'wp-gistpen', '_wpgp_gist_token', false );
	}

	public function mock( $alias ) {
		try {
			$to_mock = $this->app->fetch( $alias );

			return Mockery::mock( get_class( $to_mock ) );
		} catch ( UndefinedAliasException $e ) {
			return Mockery::mock( $alias );
		}
	}

	public function create_post_and_children() {
		$this->gistpen = $this->factory->gistpen->create_and_get();

		$this->files = $this->factory->gistpen->create_many( 3, array(
			'post_parent' => $this->gistpen->ID
		) );

		foreach ( $this->files as $file ) {
			wp_set_object_terms( $file, 'php', 'wpgp_language', false );
		}

		update_post_meta( $this->gistpen->ID, '_wpgp_gist_id', 'none' );
	}

}
