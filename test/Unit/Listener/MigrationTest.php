<?php
namespace Intraxia\Jaxion\Test\Unit\Listener;

use Intraxia\Gistpen\Listener\Migration;
use Intraxia\Gistpen\Test\Unit\TestCase;
use Intraxia\Gistpen\Model\Repo;
use Intraxia\Gistpen\Model\Blob;
use Intraxia\Gistpen\Model\Language;
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

		$this->migration = $this->app->make( Migration::class );
	}

	public function test_should_update_to_1_0_0() {
		$old_opts = array(
			'_wpgp_gistpen_highlighter_theme' => 'okaidia',
			'_wpgp_gistpen_line_numbers'      => 'on',
			'_wpgp_gist_token'                => '123456789zxcvbnmasdfghjklqwertyuiop',
		);
		$slug     = $this->app->get( 'slug' );

		update_option( 'wp_gistpen_version', '0.5.8' );
		update_option( 'wp-gistpen', $old_opts );

		$this->migration->run();

		$this->assertEquals(
			get_option( 'wp_gistpen_version' ),
			$this->app->get( 'version' )
		);
		$this->assertEquals( array(
			'prism' => array(
				'theme'           => $old_opts['_wpgp_gistpen_highlighter_theme'],
				'line-numbers'    => $old_opts['_wpgp_gistpen_line_numbers'],
				'show-invisibles' => 'off',
			),
		), get_option( $slug . '_no_priv' ) );
		$this->assertEquals(
			array( 'gist' => array( 'token' => $old_opts['_wpgp_gist_token'] ) ),
			get_option( $slug . '_priv' )
		);
	}

	public function test_should_update_to_2_0_0() {
		$repo      = $this->fm->create( Repo::class );
		$none      = $this->fm->create( Language::class, [ 'slug' => 'none' ] );
		$java      = $this->fm->create( Language::class, [ 'slug' => 'java' ] );
		$none_blob = $this->fm->create( Blob::class, [
			'repo_id'  => $repo->ID,
			'language' => $none,
		] );
		$java_blob = $this->fm->create( Blob::class, [
			'repo_id'  => $repo->ID,
			'language' => $java,
		] );
		update_option( 'wp_gistpen_version', '1.0.0' );

		$this->migration->run();

		$em        = $this->app->get( 'database' );
		$none_blob = $em->find( Blob::class, $none_blob->ID, [
			'with' => 'language',
		] );
		$java_blob = $em->find( Blob::class, $java_blob->ID, [
			'with' => 'language',
		] );

		$this->assertEquals( 'plaintext', $none_blob->language->slug );
		$this->assertEquals( 'java', $java_blob->language->slug );

		$none = $em->find( Language::class, $none->ID );

		$this->assertWPError( $none );
		$this->assertEquals( 'not_found', $none->get_error_code() );
	}
}
