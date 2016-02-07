<?php
namespace Intraxia\Gistpen\Test;

use Intraxia\Gistpen\App;
use Intraxia\Jaxion\Core\UndefinedAliasException;
use Mockery;
use SimpleXMLElement;
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

	/**
	 * @var int
	 */
	protected $user_id;

	function setUp() {
		parent::setUp();
		$this->factory = new Factory;
		$this->app     = App::instance();
	}

	function tearDown() {
		parent::tearDown();
		Mockery::close();
	}

	public function mock( $alias ) {
		try {
			$to_mock = $this->app->fetch( $alias );

			return Mockery::mock( get_class( $to_mock ) );
		} catch ( UndefinedAliasException $e ) {
			return Mockery::mock( $alias );
		}
	}

	public function set_role( $role ) {
		$this->user_id = $this->factory->user->create( array( 'role' => $role ) );
		wp_set_current_user( $this->user_id );
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

	/**
	 * @source: http://www.snip2code.com/Snippet/7704/Assert-HTML-validity-with-PHPUnit-
	 */
	public function assertValidHtml( $html ) {
		$html = $this->setHtmlInput( $html );
		//exit(var_dump($html));
		// cURL
		$curl = curl_init();
		curl_setopt_array( $curl, array(
			// CURLOPT_CONNECTTIMEOUT => 1,
			CURLOPT_URL            => 'https://html5.validator.nu/',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => array(
				'out'     => 'xml',
				'content' => $html,
			),
		) );
		$response = curl_exec( $curl );
		if ( ! $response ) {
			$this->markTestIncomplete( 'Issues checking HTML validity.' );
		}
		curl_close( $curl );

		// fail if errors
		$xml               = new SimpleXMLElement( $response );
		$nonDocumentErrors = $xml->{'non-document-error'};
		$errors            = $xml->error;
		if ( count( $nonDocumentErrors ) > 0 ) {
			// indeterminate
			$this->markTestIncomplete();
		} elseif ( count( $errors ) > 0 ) {
			// invalid
			$this->fail( "HTML output did not validate." );
		}

		// valid
		$this->assertTrue( true );
	}

	/**
	 * Ensure that HTML fragments are submitted as complete webpages.
	 *
	 * @param string $value The HTML markup, either a fragment or a complete webpage.
	 * @source https://github.com/kevintweber/phpunit-markup-validators/blob/master/src/kevintweber/PhpunitMarkupValidators/Connector/HTMLConnector.php
	 *
	 * @return string
	 */
	public function setHtmlInput( $value ) {
		if ( substr( $value, 0, 15 ) !== '<!DOCTYPE html>' ) {
			$value = '<!DOCTYPE html><html><head><meta charset="utf-8" /><title>Test</title></head><body>' . $value . '</body></html>';
		}

		return $value;
	}
}
