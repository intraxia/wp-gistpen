<?php
namespace Intraxia\Gistpen\Test\Client;

use Github\Client;
use Github\Exception\RuntimeException;
use Intraxia\Gistpen\Client\Gist;
use Intraxia\Gistpen\Test\TestCase;
use Mockery;

/**
 * @group clients
 */
class GistTest extends TestCase {
	/**
	 * @var Gist
	 */
	protected $gist;

	/**
	 * @var Mockery\MockInterface
	 */
	protected $adapter;

	/**
	 * @var Mockery\MockInterface
	 */
	protected $client;

	/**
	 * @var Mockery\MockInterface
	 */
	protected $commit;

	/**
	 * @var Mockery\MockInterface
	 */
	protected $zip;

	/**
	 * @var Mockery\MockInterface
	 */
	protected $file;

	/**
	 * @var Mockery\MockInterface
	 */
	protected $lang;

	public function setUp() {
		parent::setUp();
		$this->gist = new Gist(
			$this->adapter = $this->mock( 'facade.adapter' ),
			$this->client = $this->mock( 'Github\Client')
		);
		$this->commit = $this->mock( 'Intraxia\Gistpen\Model\Commit\Meta' );
		$this->zip = $this->mock( 'Intraxia\Gistpen\Model\Zip' );
		$this->file = $this->mock( 'Intraxia\Gistpen\Model\File' );
		$this->lang = $this->mock( 'Intraxia\Gistpen\Model\Language' );

		$this->client
			->shouldReceive( 'api' )
			->andReturn( $this->client )
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
		$this->client
			->shouldReceive( 'authenticate' )
			->with( '1234', null, Client::AUTH_HTTP_TOKEN )
			->once();
		$this->client
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
		$this->client
			->shouldReceive( 'authenticate' )
			->with( '1234', null, Client::AUTH_HTTP_TOKEN )
			->once();
		$this->client
			->shouldReceive( 'show' )
			->once()
			->andThrow( new RuntimeException );

		$this->assertFalse( $this->gist->is_token_valid() );
		$this->assertInstanceOf( '\WP_Error', $this->gist->get_error() );
		$this->assertFalse( get_transient( '_wpgp_github_token_user_info' ) );
	}

	public function test_create_should_fail_if_not_ready() {
		$result = $this->gist->create( $this->commit );

		$this->assertFalse( $result );
		$this->assertEquals( 'noToken', $this->gist->get_error()
			->get_error_code() );
	}

	public function test_create_should_set_error_and_return_false_on_api_failure() {
		cmb2_update_option( 'wp-gistpen', '_wpgp_gist_token', '1234' );

		$this->adapter
			->shouldReceive( 'build' )
			->with( 'gist' )
			->once()
			->andReturn( $this->adapter );
		$this->adapter
			->shouldReceive( 'create_by_commit' )
			->with( $this->commit )
			->once()
			->andReturn( array() );
		$this->client
			->shouldReceive( 'authenticate' )
			->once();
		$this->client
			->shouldReceive( 'create' )
			->with( array() )
			->once()
			->andThrow( new RuntimeException( 'Some Error Occurred', 1234 ) );

		$result = $this->gist->create( $this->commit );

		$this->assertFalse( $result );
		$this->assertEquals( 'Some Error Occurred', $this->gist->get_error()
			->get_error_message() );
	}

	public function test_should_return_new_gist_data() {
		cmb2_update_option( 'wp-gistpen', '_wpgp_gist_token', '1234' );

		$this->adapter
			->shouldReceive( 'build' )
			->with( 'gist' )
			->once()
			->andReturn( $this->adapter );
		$this->adapter
			->shouldReceive( 'create_by_commit' )
			->once()
			->andReturn( array() );
		$this->client
			->shouldReceive( 'authenticate' )
			->once();
		$this->client
			->shouldReceive( 'create' )
			->once()
			->andReturn( array( 'id' => '1234' ) );

		$result = $this->gist->create( $this->commit );

		$this->assertInternalType( 'array', $result );
		$this->assertEquals( '1234', $result['id'] );
	}

	public function test_update_should_fail_if_not_ready() {
		$result = $this->gist->update( $this->commit );

		$this->assertFalse( $result );
		$this->assertEquals( 'noToken', $this->gist->get_error()
			->get_error_code() );
	}

	public function test_update_should_fail_on_api_failure() {
		cmb2_update_option( 'wp-gistpen', '_wpgp_gist_token', '1234' );

		$this->adapter
			->shouldReceive( 'build' )
			->with( 'gist' )
			->once()
			->andReturn( $this->adapter );
		$this->adapter
			->shouldReceive( 'update_by_commit' )
			->once()
			->andReturn( array() );
		$this->commit
			->shouldReceive( 'get_head_gist_id' )
			->once()
			->andReturn( 'gist_id' );
		$this->client
			->shouldReceive( 'authenticate' )
			->once();
		$this->client
			->shouldReceive( 'update' )
			->once()
			->withArgs( array( 'gist_id', array() ) )
			->andThrow( new RuntimeException );

		$this->assertFalse( $this->gist->update( $this->commit ) );
		$this->assertInstanceOf( 'WP_Error', $this->gist->get_error() );
	}

	public function test_update_should_return_updated_gist_data() {
		cmb2_update_option( 'wp-gistpen', '_wpgp_gist_token', '1234' );

		$this->adapter
			->shouldReceive( 'build' )
			->with( 'gist' )
			->once()
			->andReturn( $this->adapter );
		$this->adapter
			->shouldReceive( 'update_by_commit' )
			->once()
			->andReturn( array() );
		$this->client
			->shouldReceive( 'authenticate' )
			->once()
			->shouldReceive( 'update' )
			->once()
			->withArgs( array( 'gist_id', array() ) )
			->andReturn( array( 'id' => '1234' ) );
		$this->commit
			->shouldReceive( 'get_head_gist_id' )
			->once()
			->andReturn( 'gist_id' );

		$result = $this->gist->update( $this->commit );

		$this->assertEquals( array( 'id' => '1234' ), $result );
	}

	public function test_all_should_fail_if_not_ready() {
		$this->assertFalse( $this->gist->all() );
		$this->assertEquals( 'No GitHub OAuth token found.', $this->gist->get_error()
			->get_error_message() );
	}

	public function test_all_should_fail_on_api_failure() {
		cmb2_update_option( 'wp-gistpen', '_wpgp_gist_token', '1234' );

		$this->client
			->shouldReceive( 'authenticate' )
			->once();
		$pager = $this->mock( 'overload:Github\ResultPager' );
		$pager->shouldReceive( 'fetchAll' )
			->with( $this->client, 'all' )
			->andThrow( new RuntimeException( 'Some Error Occurred', 1234 ) );

		$this->assertFalse( $this->gist->all() );
		$this->assertEquals( 'Some Error Occurred', $this->gist->get_error()
			->get_error_message() );
	}

	public function test_all_should_return_array_of_IDs() {
		cmb2_update_option( 'wp-gistpen', '_wpgp_gist_token', '1234' );

		$this->client
			->shouldReceive( 'authenticate' )
			->once();
		$pager = $this->mock( 'overload:Github\ResultPager' );
		$pager->shouldReceive( 'fetchAll' )
			->with( $this->client, 'all' )
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

		$this->client
			->shouldReceive( 'authenticate' )
			->once();
		$this->client
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
		$this->client
			->shouldReceive( 'authenticate' )
			->once()
			->shouldReceive( 'show' )
			->with( '1234' )
			->once()
			->andReturn( $response );
		$this->adapter
			->shouldReceive( 'build' )
			->times( 3 )
			->andReturn( $this->adapter );
		$this->adapter
			->shouldReceive( 'by_gist' )
			->with( $response )
			->andReturn( $this->zip );
		$this->adapter
			->shouldReceive( 'by_gist' )
			->with( $response['files']['test.php'] )
			->andReturn( $this->file );
		$this->adapter
			->shouldReceive( 'by_gist' )
			->with( $response['files']['test.php']['language'] )
			->andReturn( $this->lang );
		$this->zip
			->shouldReceive( 'add_file' )
			->with( $this->file );
		$this->file
			->shouldReceive( 'set_language' )
			->with( $this->lang );

		$gist = $this->gist->get( '1234' );

		$this->assertSame( $this->zip, $gist['zip'] );
		$this->assertEquals( '1234', $gist['version'] );
	}

	public function tearDown() {
		parent::tearDown();
	}
}
