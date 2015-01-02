<?php
use WP_Gistpen\Adapter\State as StateAdapter;
/**
 * @group  adapters
 */
class WP_Gistpen_Adapter_State_Test extends WP_Gistpen_UnitTestCase {

	function setUp() {
		parent::setUp();
		$this->adapter = new StateAdapter( WP_Gistpen::$plugin_name, WP_Gistpen::$version );
	}

	function test_build_blank() {
		$state = $this->adapter->blank();

		$this->assertInstanceOf( 'WP_Gistpen\Model\Commit\State', $state );
	}

	function test_build_by_array_complete() {
		$data = array(
			'slug' => 'test-this',
			'code' => 'echo $stuff;',
			'ID'   => 123
		);

		$state = $this->adapter->by_array( $data );

		$this->assertEquals( 'test-this', $state->get_slug() );
		$this->assertEquals( 'echo $stuff;', $state->get_code() );
		$this->assertEquals( 123, $state->get_ID() );
	}

	function test_build_by_array_with_extra_vars() {
		$data = array(
			'slug'  => 'test-this',
			'code'  => 'echo $stuff;',
			'ID'    => 123,
			'extra' => 'stuff'
		);

		$state = $this->adapter->by_array( $data );

		$this->assertEquals( 'test-this', $state->get_slug() );
		$this->assertEquals( 'echo $stuff;', $state->get_code() );
		$this->assertEquals( 123, $state->get_ID() );
	}

	function test_build_by_array_with_only_ID() {
		$data = array(
			'ID'    => 123
		);

		$state = $this->adapter->by_array( $data );

		$this->assertEquals( '', $state->get_slug() );
		$this->assertEquals( '', $state->get_code() );
		$this->assertEquals( 123, $state->get_ID() );
	}

	function test_build_by_array_with_only_code() {
		$data = array(
			'code'  => 'echo $stuff;'
		);

		$state = $this->adapter->by_array( $data );

		$this->assertEquals( '', $state->get_slug() );
		$this->assertEquals( 'echo $stuff;', $state->get_code() );
		$this->assertEquals( null, $state->get_ID() );
	}

	function test_build_by_array_with_only_slug() {
		$data = array(
			'slug'  => 'test-this'
		);

		$state = $this->adapter->by_array( $data );

		$this->assertEquals( 'test-this', $state->get_slug() );
		$this->assertEquals( '', $state->get_code() );
		$this->assertEquals( null, $state->get_ID() );
	}

	function test_build_by_post() {
		$post = new WP_Post( new stdClass );
		$post->post_content = 'echo $stuff;';
		$post->post_title = 'Test This';
		$post->ID = 123;

		$state = $this->adapter->by_post( $post );

		$this->assertEquals( 'test-this', $state->get_slug() );
		$this->assertEquals( 'echo $stuff;', $state->get_code() );
		$this->assertEquals( 123, $state->get_ID() );
	}

	function tearDown() {
		parent::tearDown();
	}
}
