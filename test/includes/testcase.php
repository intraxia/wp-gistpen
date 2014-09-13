<?php

class WP_Gistpen_UnitTestCase extends WP_UnitTestCase {

	public $mock_lang;
	public $mock_post;
	public $mock_file;
	public $gistpen;
	public $files;

	function setUp() {
		parent::setUp();
		$this->factory = new WP_Gistpen_UnitTest_Factory;

		$this->mock_lang = $this->getMockBuilder( 'WP_Gistpen_Language' )->disableOriginalConstructor()->getMock();
		$this->mock_post = $this->getMockBuilder( 'WP_Gistpen_Post' )->disableOriginalConstructor()->getMock();
		$this->mock_file = $this->getMockBuilder( 'WP_Gistpen_File' )->disableOriginalConstructor()->getMock();
	}

	function create_post_and_children() {
		$this->gistpen = $this->factory->gistpen->create_and_get();

		$this->files = $this->factory->gistpen->create_many( 3, array(
			'post_parent' => $this->gistpen->ID
		) );

		foreach ( $this->files as $file ) {
			wp_set_object_terms( $file, 'php', 'language', false );
		}
	}

	// Source: http://stackoverflow.com/questions/5010300/best-practices-to-test-protected-methods-with-phpunit-on-abstract-classes
	protected static function callProtectedMethod( $name, $classname, $params ) {
		$class = new ReflectionClass($classname);
		$method = $class->getMethod($name);
		$method->setAccessible(true);
		$obj = new $classname($params);
		return $method->invokeArgs($obj, $params);
	}
}
