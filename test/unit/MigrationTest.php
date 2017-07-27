<?php
namespace Intraxia\Jaxion\Test;

use Intraxia\Gistpen\Migration;
use Intraxia\Gistpen\Test\TestCase;
use Mockery\Mock;

class MigrationTest extends TestCase {
	/**
	 * @var Migration
	 */
	protected $migration;

	/**
	 * @var Mock
	 */
	protected $em;

	public function setUp() {
		parent::setUp();

		$this->migration = new Migration(
			$this->mock( 'facade.database' ),
			$this->mock( 'facade.adapter' ),
			$this->em = $this->mock( 'database' ),
			$this->app->fetch( 'slug' ),
			'1.0.0'
		);
	}

	public function test_should_update_to_1_0_0() {
		$this->em->shouldReceive( 'migrate' )->once();
		$old_opts = array(
			'_wpgp_gistpen_highlighter_theme' => 'okaidia',
			'_wpgp_gistpen_line_numbers'      => 'on',
			'_wpgp_gist_token'                => '123456789zxcvbnmasdfghjklqwertyuiop'
		);
		$slug = $this->app->fetch( 'slug' );

		update_option( 'wp_gistpen_version', '0.5.8' );
		update_option( 'wp-gistpen', $old_opts );

		$this->migration->run();

		$this->assertEquals( get_option( 'wp_gistpen_version' ), '1.0.0' );
		$this->assertEquals( array(
			'prism' => array(
				'theme'           => $old_opts['_wpgp_gistpen_highlighter_theme'],
				'line-numbers'    => $old_opts['_wpgp_gistpen_line_numbers'],
				'show-invisibles' => 'off',
			)
		), get_option( $slug . '_no_priv' ) );
		$this->assertEquals(
			array( 'gist' => array( 'token' => $old_opts['_wpgp_gist_token'] ) ),
			get_option( $slug . '_priv' )
		);
	}
}
