<?php

class WP_Gistpen_UnitTestCase extends WP_UnitTestCase {
	function setUp() {
		parent::setUp();
		$this->factory = new WP_Gistpen_UnitTest_Factory;
	}
}
