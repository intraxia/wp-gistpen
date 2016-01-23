<?php
namespace Intraxia\Gistpen\Test\Client;

use Github\Client;
use Github\Exception\RuntimeException;
use Intraxia\Gistpen\Client\Gist;
use Mockery;

/**
 * @group clients
 */
class GistTest extends \WP_Gistpen_UnitTestCase {
	/**
	 * @var Gist
	 */
	public $gist;

	public function setUp() {
		parent::setUp();
		$this->gist = new Gist( $this->mock_adapter, $this->mock_github_client );
		$this->mock_github_client
			->shouldReceive( 'api' )
			->andReturn( $this->mock_github_client )
			->byDefault();
	}

	public function test_should_set_not_ready_if_no_token() {
		$this->gist->set_token( '' );

		$ready = new \ReflectionProperty( $this->gist, 'ready' );
		$ready->setAccessible( true );

		$this->assertFalse( $ready->getValue( $this->gist ) );
	}

	public function test_should_check_token_by_user() {
		cmb2_update_option( 'wp-gistpen', '_wpgp_gist_token', '1234' );
		$response = array(
			'username' => 'mAAdhaTTah',
			'email'    => 'jamesorodig@gmail.com',
		);
		$this->mock_github_client
			->shouldReceive( 'authenticate' )
			->with( '1234', null, Client::AUTH_HTTP_TOKEN )
			->once();
		$this->mock_github_client
			->shouldReceive( 'show' )
			->once()
			->andReturn( $response );

		$this->assertTrue( $this->gist->is_token_valid() );
		$this->assertEquals( $response, get_transient( '_wpgp_github_token_user_info' ) );
	}

	public function test_should_fail_if_token_not_set() {
		$this->assertFalse( $this->gist->is_token_valid() );
		$this->assertInstanceOf( '\WP_Error', $this->gist->get_error() );
	}

	public function test_should_fail_if_token_invalid() {
		cmb2_update_option( 'wp-gistpen', '_wpgp_gist_token', '1234' );
		$this->mock_github_client
			->shouldReceive( 'authenticate' )
			->with( '1234', null, Client::AUTH_HTTP_TOKEN )
			->once();
		$this->mock_github_client
			->shouldReceive( 'show' )
			->once()
			->andThrow( new RuntimeException );

		$this->assertFalse( $this->gist->is_token_valid() );
		$this->assertInstanceOf( '\WP_Error', $this->gist->get_error() );
		$this->assertFalse( get_transient( '_wpgp_github_token_user_info' ) );
	}

	public function test_create_should_fail_if_not_ready() {
		$result = $this->gist->create( $this->mock_commit );

		$this->assertFalse( $result );
		$this->assertEquals( 'noToken', $this->gist->get_error()
			->get_error_code() );
	}

	public function test_create_should_set_error_and_return_false_on_api_failure() {
		cmb2_update_option( 'wp-gistpen', '_wpgp_gist_token', '1234' );

		$this->mock_adapter
			->shouldReceive( 'build' )
			->with( 'gist' )
			->once()
			->andReturn( $this->mock_gist_adapter );
		$this->mock_gist_adapter
			->shouldReceive( 'create_by_commit' )
			->with( $this->mock_commit )
			->once()
			->andReturn( array() );
		$this->mock_github_client
			->shouldReceive( 'authenticate' )
			->once();
		$this->mock_github_client
			->shouldReceive( 'create' )
			->with( array() )
			->once()
			->andThrow( new RuntimeException( 'Some Error Occurred', 1234 ) );

		$result = $this->gist->create( $this->mock_commit );

		$this->assertFalse( $result );
		$this->assertEquals( 'Some Error Occurred', $this->gist->get_error()
			->get_error_message() );
	}

	public function test_should_return_new_gist_data() {
		cmb2_update_option( 'wp-gistpen', '_wpgp_gist_token', '1234' );

		$this->mock_adapter
			->shouldReceive( 'build' )
			->with( 'gist' )
			->once()
			->andReturn( $this->mock_gist_adapter );
		$this->mock_gist_adapter
			->shouldReceive( 'create_by_commit' )
			->once()
			->andReturn( array() );
		$this->mock_github_client
			->shouldReceive( 'authenticate' )
			->once();
		$this->mock_github_client
			->shouldReceive( 'create' )
			->once()
			->andReturn( array( 'id' => '1234' ) );

		$result = $this->gist->create( $this->mock_commit );

		$this->assertInternalType( 'array', $result );
		$this->assertEquals( '1234', $result['id'] );
	}

	public function test_update_should_fail_if_not_ready() {
		$result = $this->gist->update( $this->mock_commit );

		$this->assertFalse( $result );
		$this->assertEquals( 'noToken', $this->gist->get_error()
			->get_error_code() );
	}

	public function test_update_should_fail_on_api_failure() {
		cmb2_update_option( 'wp-gistpen', '_wpgp_gist_token', '1234' );

		$this->mock_adapter
			->shouldReceive( 'build' )
			->with( 'gist' )
			->once()
			->andReturn( $this->mock_gist_adapter );
		$this->mock_gist_adapter
			->shouldReceive( 'update_by_commit' )
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
			->andThrow( new RuntimeException );

		$this->assertFalse( $this->gist->update( $this->mock_commit ) );
		$this->assertInstanceOf( 'WP_Error', $this->gist->get_error() );
	}

	public function test_update_should_return_updated_gist_data() {
		cmb2_update_option( 'wp-gistpen', '_wpgp_gist_token', '1234' );

		$this->mock_adapter
			->shouldReceive( 'build' )
			->with( 'gist' )
			->once()
			->andReturn( $this->mock_gist_adapter );
		$this->mock_gist_adapter
			->shouldReceive( 'update_by_commit' )
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

		$result = $this->gist->update( $this->mock_commit );

		$this->assertEquals( array( 'id' => '1234' ), $result );
	}

	public function test_all_should_fail_if_not_ready() {
		$this->assertFalse( $this->gist->all() );
		$this->assertEquals( 'No GitHub OAuth token found.', $this->gist->get_error()
			->get_error_message() );
	}

	public function test_all_should_fail_on_api_failure() {
		cmb2_update_option( 'wp-gistpen', '_wpgp_gist_token', '1234' );

		$this->mock_github_client
			->shouldReceive( 'authenticate' )
			->once();
		$pager = Mockery::mock( 'overload:Github\ResultPager' );
		$pager->shouldReceive( 'fetchAll' )
			->with( $this->mock_github_client, 'all' )
			->andThrow( new RuntimeException( 'Some Error Occurred', 1234 ) );

		$this->assertFalse( $this->gist->all() );
		$this->assertEquals( 'Some Error Occurred', $this->gist->get_error()
			->get_error_message() );
	}

	public function test_all_should_return_array_of_IDs() {
		cmb2_update_option( 'wp-gistpen', '_wpgp_gist_token', '1234' );

		$this->mock_github_client
			->shouldReceive( 'authenticate' )
			->once();
		$pager = Mockery::mock( 'overload:Github\ResultPager' );
		$pager->shouldReceive( 'fetchAll' )
			->with( $this->mock_github_client, 'all' )
			->andReturn( array(
				array( 'id' => 'first', 'code' => 'echo $truth;' ),
				array( 'id' => 'second', 'code' => 'echo $lies;' )
			) );

		$result = $this->gist->all();

		$this->assertCount( 2, $result );
		$this->assertEquals( 'first', $result[0] );
		$this->assertEquals( 'second', $result[1] );
	}

	public function test_get_should_fail_if_not_ready() {
		$this->assertFalse( $this->gist->get( '1234' ) );
		$this->assertEquals( 'No GitHub OAuth token found.', $this->gist->get_error()
			->get_error_message() );
	}

	public function test_get_should_fail_on_api_failure() {
		cmb2_update_option( 'wp-gistpen', '_wpgp_gist_token', '1234' );

		$this->mock_github_client
			->shouldReceive( 'authenticate' )
			->once();
		$this->mock_github_client
			->shouldReceive( 'show' )
			->with( '1234' )
			->andThrow( new RuntimeException( 'Some Error Occurred', 1234 ) );

		$this->assertFalse( $this->gist->get( '1234' ) );
		$this->assertEquals( 'Some Error Occurred', $this->gist->get_error()
			->get_error_message() );
	}

	public function test_get_should_return_array_with_zip_and_version() {
		$response = array(
			'history' => array(
				array( 'version' => '1234' )
			),
			'files'   => array(
				'test.php' => array(
					'language' => 'PHP',
				),
			),
		);

		cmb2_update_option( 'wp-gistpen', '_wpgp_gist_token', '1234' );
		$this->mock_github_client
			->shouldReceive( 'authenticate' )
			->once()
			->shouldReceive( 'show' )
			->with( '1234' )
			->once()
			->andReturn( $response );
		$this->mock_adapter
			->shouldReceive( 'build' )
			->times( 3 )
			->andReturn( $this->mock_adapter );
		$this->mock_adapter
			->shouldReceive( 'by_gist' )
			->with( $response )
			->andReturn( $this->mock_zip );
		$this->mock_adapter
			->shouldReceive( 'by_gist' )
			->with( $response['files']['test.php'] )
			->andReturn( $this->mock_file );
		$this->mock_adapter
			->shouldReceive( 'by_gist' )
			->with( $response['files']['test.php']['language'] )
			->andReturn( $this->mock_lang );
		$this->mock_zip
			->shouldReceive( 'add_file' )
			->with( $this->mock_file );
		$this->mock_file
			->shouldReceive( 'set_language' )
			->with( $this->mock_lang );

		$gist = $this->gist->get( '1234' );

		$this->assertSame( $this->mock_zip, $gist['zip'] );
		$this->assertEquals( '1234', $gist['version'] );
	}

	public function tearDown() {
		parent::tearDown();
	}
}
