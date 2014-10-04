<?php

/**
 * @group objects
 * @group file
 */
class WP_Gistpen_File_Test extends WP_Gistpen_UnitTestCase {

	public $file_obj;
	public $file;

	function setUp() {
		parent::setUp();
		$this->file_obj = $this->factory->gistpen->create_and_get( array( 'post_parent' => $this->factory->gistpen->create() ) );
		$this->file = new WP_Gistpen_File( $this->file_obj, $this->mock_lang, $this->mock_post  );

		$this->mock_lang
			->expects( $this->any() )
			->method( '__get' )
			->with( $this->anything() );
	}

	function test_get_post_object() {
		$this->assertInstanceOf('WP_Post', $this->file->file);
	}

	function test_get_slug() {
		$this->assertContains( 'post-title', $this->file->slug );
	}

	function test_get_filename_with_extension() {
		$this->assertContains( 'post-title', $this->file->filename );
		$this->assertContains( '.', $this->file->filename );
		$this->assertNotContains( ' ', $this->file->filename );
	}

	function test_get_code() {
		$this->assertContains( 'Post content', $this->file->code );
	}

	function test_get_post_content() {
		$this->assertValidHtml( $this->file->post_content );
		$this->assertContains( $this->file->code, $this->file->post_content );
		$this->assertContains( $this->file->code, $this->file->post_content );
	}

	function test_get_shortcode_content() {
		$this->assertValidHtml( $this->file->shortcode_content );
		$this->assertContains( $this->file->code, $this->file->shortcode_content );
		$this->assertContains( $this->file->code, $this->file->shortcode_content );
	}

	function test_update_post() {
		$this->file->slug = 'New slug';
		$this->file->code = 'echo $code';
		$this->mock_lang
			->expects($this->once())
			->method('update_post')
			->will($this->returnValue(true));

		$this->file->update_post();

		$this->assertEquals( 'new-slug', $this->file->file->post_name );
		$this->assertEquals( 'echo $code', $this->file->file->post_content );
	}

	// @source: http://www.snip2code.com/Snippet/7704/Assert-HTML-validity-with-PHPUnit-
	public function assertValidHtml($html) {
		$html = $this->setHtmlInput($html);
		//exit(var_dump($html));
		// cURL
		$curl = curl_init();
		curl_setopt_array($curl, array(
			// CURLOPT_CONNECTTIMEOUT => 1,
			CURLOPT_URL => 'http://html5.validator.nu/',
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
		if (stripos($value, 'html>') === false) {
			return '<!DOCTYPE html><html><head><meta charset="utf-8" /><title>Test</title></head><body>' . $value . '</body></html>';
		}
		else {
			return $value;
		}

	}

	function tearDown() {
		parent::tearDown();
	}
}
