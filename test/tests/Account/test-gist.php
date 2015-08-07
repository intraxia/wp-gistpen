<?php
use Intraxia\Gistpen\Account\Gist;
use Intraxia\Gistpen\Facade\Adapter;

/**
 * @group accounts
 */
class Account_Gist_Test extends \Intraxia\Gistpen\Test\UnitTestCase {

	function setUp() {
		parent::setUp();

		$this->gist = new GistStub();
		$this->gist->set_client( $this->mock_github_client );
		$this->gist->set_adapter( $this->mock_adapter );
	}

	function test_check_token_suceeds() {
		$this->mock_github_client
			->shouldReceive( 'show_me' )
			->once()
			->andReturn( array( 'user_data' ) );

		$result = $this->gist->check_token();

		$this->assertTrue( $result );
		$this->assertEquals( array( 'user_data' ), get_transient( '_wpgp_github_token_user_info' ) );
	}

	function test_check_token_catch_exception() {
		$this->mock_github_client
			->shouldReceive( 'show_me' )
			->once()
			->andThrow( new Github\Exception\RuntimeException );

		$result = $this->gist->check_token();

		$this->assertInstanceOf( 'WP_Error', $result );
		$this->assertFalse( get_transient( '_wpgp_github_token_user_info' ) );
	}

	function test_set_up_client_fails_no_token() {
		$result = $this->gist->create_gist( array() );

		$this->assertInstanceOf( 'WP_Error', $result );
	}

	function test_create_gist_catch_exception() {
		cmb2_update_option( \Gistpen::$plugin_name, '_wpgp_gist_token', '1234' );

		$this->mock_adapter
			->shouldReceive( 'build' )
			->with( 'gist' )
			->once()
			->andReturn( $this->mock_gist_adapter );
		$this->mock_gist_adapter
			->shouldReceive( 'create_by_commit')
			->once()
			->andReturn( array() );
		$this->mock_github_client
			->shouldReceive( 'authenticate' )
			->once();
		$this->mock_github_client
			->shouldReceive( 'create' )
			->once()
			->andThrow( new Github\Exception\RuntimeException );

		$result = $this->gist->create_gist( $this->mock_commit );

		$this->assertInstanceOf( 'WP_Error', $result );
	}

	function test_create_gist_succeed() {
		cmb2_update_option( \Gistpen::$plugin_name, '_wpgp_gist_token', '1234' );

		$this->mock_adapter
			->shouldReceive( 'build' )
			->with( 'gist' )
			->once()
			->andReturn( $this->mock_gist_adapter );
		$this->mock_gist_adapter
			->shouldReceive( 'create_by_commit')
			->once()
			->andReturn( array() );
		$this->mock_github_client
			->shouldReceive( 'authenticate' )
			->once();
		$this->mock_github_client
			->shouldReceive( 'create' )
			->once()
			->andReturn( array( 'id' => '1234' ) );

		$result = $this->gist->create_gist( $this->mock_commit );

		$this->assertEquals( '1234', $result['id'] );
	}

	function test_update_gist_catch_exception() {
		cmb2_update_option( \Gistpen::$plugin_name, '_wpgp_gist_token', '1234' );

		$this->mock_adapter
			->shouldReceive( 'build' )
			->with( 'gist' )
			->once()
			->andReturn( $this->mock_gist_adapter );
		$this->mock_gist_adapter
			->shouldReceive( 'update_by_commit')
			->once()
			->andReturn( array() );
		$this->mock_commit
			->shouldReceive( 'get_head_gist_id' )
			->once()
			->andReturn( 'gist_id' );
		$this->mock_github_client
			->shouldReceive( 'authenticate' )
			->once();
		$this->mock_github_client
			->shouldReceive( 'update' )
			->once()
			->withArgs( array( 'gist_id', array() ) )
			->andThrow( new Github\Exception\RuntimeException );

		$result = $this->gist->update_gist( $this->mock_commit );

		$this->assertInstanceOf( 'WP_Error', $result );
	}

	function test_update_gist_succeed() {
		cmb2_update_option( \Gistpen::$plugin_name, '_wpgp_gist_token', '1234' );

		$this->mock_adapter
			->shouldReceive( 'build' )
			->with( 'gist' )
			->once()
			->andReturn( $this->mock_gist_adapter );
		$this->mock_gist_adapter
			->shouldReceive( 'update_by_commit')
			->once()
			->andReturn( array() );
		$this->mock_github_client
			->shouldReceive( 'authenticate' )
			->once()
			->shouldReceive( 'update' )
			->once()
			->withArgs( array( 'gist_id', array() ) )
			->andReturn( array( 'id' => '1234' ) );
		$this->mock_commit
			->shouldReceive( 'get_head_gist_id' )
			->once()
			->andReturn( 'gist_id' );

		$result = $this->gist->update_gist( $this->mock_commit );

		$this->assertEquals( array( 'id' => '1234' ), $result );
	}

	function tearDown() {
		parent::tearDown();
	}
}

class GistStub extends Gist {

	public function set_client( $client ) {
		$this->client = $client;
	}

	public function set_adapter( $adapter ) {
		$this->adapter = $adapter;
	}

	protected function call() {
		return $this->client;
	}

	protected function show_me() {
		return $this->client->show_me();
	}

}
