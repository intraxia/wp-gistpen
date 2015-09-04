<?php
use Intraxia\Gistpen\Adapter\History as HistoryAdapter;
/**
 * @group  adapters
 */
class Adapter_History_Test extends \Intraxia\Gistpen\Test\UnitTestCase {

	function setUp() {
		parent::setUp();
		$this->adapter = new HistoryAdapter();
	}

	function test_build_blank() {
		$history = $this->adapter->blank();

		$this->assertInstanceOf( 'Intraxia\Gistpen\Collection\History', $history );
	}

	function tearDown() {
		parent::tearDown();
	}
}
