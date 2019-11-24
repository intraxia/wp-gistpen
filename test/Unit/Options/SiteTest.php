<?php
namespace Intraxia\Gistpen\Test\Unit\Options;

use Intraxia\Gistpen\Options\Site;
use Intraxia\Gistpen\Test\Unit\TestCase;

class SiteTest extends TestCase {
	/**
	 * @var Site
	 */
	protected $site;

	/**
	 * @var array
	 */
	protected $unprotected = array(
		'prism' => array(
			'theme'           => 'default',
			'line-numbers'    => false,
			'show-invisibles' => false,
		),
	);

	/**
	 * @var array
	 */
	protected $protected = array(
		'prism' => array(
			'theme'           => 'default',
			'line-numbers'    => false,
			'show-invisibles' => false,
		),
		'gist'  => array(
			'token' => '',
		),
	);

	public function setUp() {
		parent::setUp();
		$this->site = new Site( 'wp-gistpen' );
	}

	public function tearDown() {
		parent::tearDown();

		delete_option( 'wp-gistpen' );
	}

	public function test_should_retrieve_public_user_options() {
		$this->set_role( 'subscriber' );

		$this->assertSame( $this->unprotected, $this->site->all() );
	}

	public function test_should_reset_mangled_options() {
		$this->set_role( 'subscriber' );
		update_option( 'wp-gistpen_no_priv', 'something went wrong' );

		$this->assertSame( $this->unprotected, $this->site->all() );

		update_option( 'wp-gistpen_no_priv', array() );

		$this->assertSame( $this->unprotected, $this->site->all() );
	}

	public function test_should_retrieve_protected_options() {
		$this->set_role( 'administrator' );

		$this->assertEquals( $this->protected, $this->site->all() );
	}

	public function test_should_get_unprotected_option() {
		$this->set_role( 'subscriber' );

		$prism = $this->site->get( 'prism' );

		$this->assertSame( $this->unprotected['prism'], $prism );
	}

	public function test_should_hide_protected_option() {
		$this->set_role( 'subscriber' );

		$gist = $this->site->get( 'gist' );

		$this->assertNull( $gist );
	}

	public function test_should_throw_on_invalid_key() {
		$this->set_role( 'administrator' );

		$this->expectException( \InvalidArgumentException::class );

		$this->site->get( 'random' );
	}

	public function test_should_get_protected_key() {
		$this->set_role( 'administrator' );

		$gist = $this->site->get( 'gist' );

		$this->assertSame( $this->protected['gist'], $gist );
	}

	public function test_should_fail_to_update_without_perms() {
		$this->set_role( 'subscriber' );

		$this->site->patch( array( 'prism' => array( 'theme' => 'xonokai' ) ) );

		$this->assertSame( $this->unprotected, $this->site->all() );
	}

	public function test_should_ignore_invalid_argument() {
		$this->set_role( 'administrator' );

		$this->site->patch( array( 'random_key' => array( 'some_prop' => false ) ) );

		$this->assertSame( $this->protected, $this->site->all() );
	}

	public function test_should_update_valid_prism_key() {
		$this->set_role( 'administrator' );

		$this->site->patch( array( 'prism' => array( 'theme' => 'xonokai' ) ) );

		$site = $this->site->all();
		$this->assertSame( 'xonokai', $site['prism']['theme'] );
	}

	public function test_should_update_boolean_prism_key() {
		$this->set_role( 'administrator' );

		$this->site->patch( array( 'prism' => array( 'line-numbers' => true ) ) );

		$site = $this->site->all();
		$this->assertSame( true, $site['prism']['line-numbers'] );
	}

	public function test_should_ignore_invalid_prism_key() {
		$this->set_role( 'administrator' );

		$this->site->patch( array( 'prism' => array( 'key' => 'value' ) ) );

		$this->assertSame( $this->protected, $this->site->all() );
	}

	public function test_should_update_valid_gist_key() {
		$this->set_role( 'administrator' );

		$this->site->patch( array( 'gist' => array( 'token' => '123456789asghskdjfhka' ) ) );

		$site = $this->site->all();
		$this->assertSame( '123456789asghskdjfhka', $site['gist']['token'] );
	}
}
