<?php
use WP_Gistpen\Adapter\History as HistoryAdapter;
/**
 * @group  adapters
 */
class WP_Gistpen_Adapter_History_Test extends WP_Gistpen_UnitTestCase {

	function setUp() {
		parent::setUp();
		$this->adapter = new HistoryAdapter();
	}

	function test_build_blank() {
		$history = $this->adapter->blank();

		$this->assertInstanceOf( 'WP_Gistpen\Collection\History', $history );
	}

	function tearDown() {
		parent::tearDown();
	}
}
