<?php
namespace Intraxia\Gistpen\Test;

use Gistpen;
use Mockery as m;
use ReflectionClass;
use SimpleXMLElement;
use WP_Ajax_UnitTestCase;

class UnitTestCase extends WP_Ajax_UnitTestCase
{
	public $mock_lang;
	public $mock_post;
	public $mock_file;
	public $gistpen;
	public $files;
    public $mock_zip;
    public $mock_history;
    public $mock_commit;
    public $mock_state;
    public $mock_sync;
    public $mock_gist_adapter;
    public $mock_database;
    public $mock_adapter;
    public $mock_github_client;

    public function setUp()
    {
        parent::setUp();
        $this->factory = new UnitTest_Factory;

        // Mock models
        $this->mock_lang = m::mock('\Intraxia\Gistpen\Model\Language');
        $this->mock_zip = m::mock('\Intraxia\Gistpen\Model\Zip');
        $this->mock_file = m::mock('\Intraxia\Gistpen\Model\File');
        $this->mock_history = m::mock('\Intraxia\Gistpen\Collection\History');
        $this->mock_commit = m::mock('\Intraxia\Gistpen\Model\Commit\Meta');
        $this->mock_state = m::mock('\Intraxia\Gistpen\Model\Commit\State');

        // Mock controllers
        $this->mock_sync = m::mock('\Intraxia\Gistpen\Controller\Sync');

        // Mock adapters
        $this->mock_gist_adapter = m::mock('\Intraxia\Gistpen\Adapter\Gist');

        // Mock Facades
        $this->mock_database = m::mock('\Intraxia\Gistpen\Facade\Database');
        $this->mock_adapter = m::mock('\Intraxia\Gistpen\Facade\Adapter');

        // 3rd Party dependencies
        $this->mock_github_client = m::mock('\Github\Client');
    }

	function tearDown()
    {
        parent::tearDown();

		m::close();
        cmb2_update_option(\Gistpen::$plugin_name, '_wpgp_gist_token', false);
	}

	function create_post_and_children() {
		$this->gistpen = $this->factory->gistpen->create_and_get();

		$this->files = $this->factory->gistpen->create_many( 3, array(
			'post_parent' => $this->gistpen->ID
		) );

		foreach ( $this->files as $file ) {
			wp_set_object_terms( $file, 'php', 'wpgp_language', false );
		}

		update_post_meta( $this->gistpen->ID, '_wpgp_gist_id', 'none' );
	}

	// Source: http://stackoverflow.com/questions/5010300/best-practices-to-test-protected-methods-with-phpunit-on-abstract-classes
	protected static function callProtectedMethod( $name, $classname, $params ) {
        $class = new ReflectionClass($classname);
		$method = $class->getMethod($name);
		$method->setAccessible(true);
		$obj = new $classname($params);
		return $method->invokeArgs($obj, $params);
	}

	// @source: http://www.snip2code.com/Snippet/7704/Assert-HTML-validity-with-PHPUnit-
	public function assertValidHtml($html) {
		$html = $this->setHtmlInput($html);
		// cURL
		$curl = curl_init();
		curl_setopt_array($curl, array(
			// CURLOPT_CONNECTTIMEOUT => 1,
			CURLOPT_URL => 'https://html5.validator.nu/',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => array(
				'out' => 'xml',
				'content' => $html,
			),
		));
		$response = curl_exec($curl);
		if (!$response) {
			$this->markTestIncomplete('Issues checking HTML validity.');
		}
		curl_close($curl);

		// fail if errors
		$xml = new SimpleXMLElement($response);
		$nonDocumentErrors = $xml->{'non-document-error'};
		$errors = $xml->error;
		if (count($nonDocumentErrors) > 0) {
			// indeterminate
			$this->markTestIncomplete();
		} elseif (count($errors) > 0) {
			// invalid
			$this->fail("HTML output did not validate.");
		}

		// valid
		$this->assertTrue(true);
	}

	/**
	 * Ensure that HTML fragments are submitted as complete webpages.
	 *
	 * @param string $value The HTML markup, either a fragment or a complete webpage.
	 * @source https://github.com/kevintweber/phpunit-markup-validators/blob/master/src/kevintweber/PhpunitMarkupValidators/Connector/HTMLConnector.php
	 */
	public function setHtmlInput($value) {
		if ( substr( $value, 0, 15 ) !== '<!DOCTYPE html>' ) {
			$value =  '<!DOCTYPE html><html><head><meta charset="utf-8" /><title>Test</title></head><body>' . $value . '</body></html>';
		}

		return $value;
	}
}
