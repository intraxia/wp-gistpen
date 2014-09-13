<?php

class WP_Gistpen_UnitTestCase extends WP_UnitTestCase {

	public $mock_lang;
	public $mock_post;
	public $mock_file;

	function setUp() {
		parent::setUp();
		$this->factory = new WP_Gistpen_UnitTest_Factory;

		$this->mock_lang = $this->getMockBuilder( 'WP_Gistpen_Language' )->disableOriginalConstructor()->getMock();
		$this->mock_post = $this->getMockBuilder( 'WP_Gistpen_Post' )->disableOriginalConstructor()->getMock();
		$this->mock_file = $this->getMockBuilder( 'WP_Gistpen_File' )->disableOriginalConstructor()->getMock();
	}
}
