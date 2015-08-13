<?php
namespace Intraxia\Gistpen\Test\Client;

use Github\Client;
use Github\Exception\RuntimeException;
use Intraxia\Gistpen\Client\Gist;
use Intraxia\Gistpen\Test\UnitTestCase;
use Mockery;

/**
 * @group clients
 */
class GistTest extends UnitTestCase
{
    /**
     * @var Gist
     */
    public $gist;

    public function setUp()
    {
        parent::setUp();
        $this->gist = new Gist($this->mock_adapter, $this->mock_github_client);
        $this->mock_github_client
            ->shouldReceive('api')
            ->andReturn($this->mock_github_client)
            ->byDefault();
    }

    public function testShouldSetNotReadyIfNoToken()
    {
        $this->gist->setToken('');

        $ready = new \ReflectionProperty($this->gist, 'ready');
        $ready->setAccessible(true);

        $this->assertFalse($ready->getValue($this->gist));
    }

    public function testShouldCheckTokenByUser()
    {
        cmb2_update_option(\Gistpen::$plugin_name, '_wpgp_gist_token', '1234');
        $response = array(
            'username' => 'mAAdhaTTah',
            'email'    => 'jamesorodig@gmail.com',
        );
        $this->mock_github_client
            ->shouldReceive('authenticate')
            ->with('1234', null, Client::AUTH_HTTP_TOKEN)
            ->once();
        $this->mock_github_client
            ->shouldReceive('show')
            ->once()
            ->andReturn($response);

        $this->assertTrue($this->gist->isTokenValid());
        $this->assertEquals($response, get_transient('_wpgp_github_token_user_info'));
    }

    public function testShouldFailIfTokenNotSet()
    {
        $this->assertFalse($this->gist->isTokenValid());
        $this->assertInstanceOf('\WP_Error', $this->gist->getError());
    }

    public function testShouldFailIfTokenInvalid()
    {
        cmb2_update_option(\Gistpen::$plugin_name, '_wpgp_gist_token', '1234');
        $this->mock_github_client
            ->shouldReceive('authenticate')
            ->with('1234', null, Client::AUTH_HTTP_TOKEN)
            ->once();
        $this->mock_github_client
            ->shouldReceive('show')
            ->once()
            ->andThrow(new RuntimeException);

        $this->assertFalse($this->gist->isTokenValid());
        $this->assertInstanceOf('\WP_Error', $this->gist->getError());
        $this->assertFalse(get_transient('_wpgp_github_token_user_info'));
    }

    public function testCreateShouldFailIfNotReady()
    {
        $result = $this->gist->create($this->mock_commit);

        $this->assertFalse($result);
        $this->assertEquals('noToken', $this->gist->getError()->get_error_code());
    }

    public function testCreateShouldSetErrorAndReturnFalseOnApiFailure()
    {
        cmb2_update_option(\Gistpen::$plugin_name, '_wpgp_gist_token', '1234');

        $this->mock_commit
            ->shouldReceive('toGist')
            ->once()
            ->andReturn(array());
        $this->mock_github_client
            ->shouldReceive('authenticate')
            ->once();
        $this->mock_github_client
            ->shouldReceive('create')
            ->with(array())
            ->once()
            ->andThrow(new RuntimeException('Some Error Occurred', 1234));

        $result = $this->gist->create($this->mock_commit);

        $this->assertFalse($result);
        $this->assertEquals('Some Error Occurred', $this->gist->getError()->get_error_message());
    }

    public function testShouldReturnNewGistData()
    {
        cmb2_update_option(\Gistpen::$plugin_name, '_wpgp_gist_token', '1234');

        $this->mock_github_client
            ->shouldReceive('authenticate')
            ->once();
        $this->mock_github_client
            ->shouldReceive('create')
            ->once()
            ->andReturn(array('id' => '1234'));
        $this->mock_commit
            ->shouldReceive('toGist')
            ->andReturn(array());

        $result = $this->gist->create($this->mock_commit);

        $this->assertInternalType('array', $result);
        $this->assertEquals('1234', $result['id']);
    }

    public function testUpdateShouldFailIfNotReady()
    {
        $result = $this->gist->update($this->mock_commit);

        $this->assertFalse($result);
        $this->assertEquals('noToken', $this->gist->getError()->get_error_code());
    }

    public function testUpdateShouldFailOnApiFailure()
    {
        cmb2_update_option(\Gistpen::$plugin_name, '_wpgp_gist_token', '1234');

        $this->mock_commit
            ->shouldReceive('toGist')
            ->once()
            ->andReturn(array());
        $this->mock_commit
            ->shouldReceive('getGistSha')
            ->once()
            ->andReturn('gist_id');
        $this->mock_github_client
            ->shouldReceive('authenticate')
            ->once();
        $this->mock_github_client
            ->shouldReceive('update')
            ->once()
            ->withArgs(array('gist_id', array()))
            ->andThrow(new RuntimeException);

        $this->assertFalse($this->gist->update($this->mock_commit));
        $this->assertInstanceOf('WP_Error', $this->gist->getError());
    }

    public function testUpdateShouldReturnUpdatedGistData()
    {
        cmb2_update_option(\Gistpen::$plugin_name, '_wpgp_gist_token', '1234');

        $this->mock_commit
            ->shouldReceive('toGist')
            ->once()
            ->andReturn(array());
        $this->mock_github_client
            ->shouldReceive('authenticate')
            ->once()
            ->shouldReceive('update')
            ->once()
            ->withArgs(array('gist_id', array()))
            ->andReturn(array('id' => '1234'));
        $this->mock_commit
            ->shouldReceive('getGistSha')
            ->once()
            ->andReturn('gist_id');

        $result = $this->gist->update($this->mock_commit);

        $this->assertEquals(array('id' => '1234'), $result);
    }

    public function testAllShouldFailIfNotReady()
    {
        $this->assertFalse($this->gist->all());
        $this->assertEquals('No GitHub OAuth token found.', $this->gist->getError()->get_error_message());
    }

    public function testAllShouldFailOnApiFailure()
    {
        cmb2_update_option(\Gistpen::$plugin_name, '_wpgp_gist_token', '1234');

        $this->mock_github_client
            ->shouldReceive('authenticate')
            ->once();
        $pager = Mockery::mock('overload:Github\ResultPager');
        $pager->shouldReceive('fetchAll')
              ->with($this->mock_github_client, 'all')
              ->andThrow(new RuntimeException('Some Error Occurred', 1234));

        $this->assertFalse($this->gist->all());
        $this->assertEquals('Some Error Occurred', $this->gist->getError()->get_error_message());
    }

    public function testAllShouldReturnArrayOfIDs()
    {
        cmb2_update_option(\Gistpen::$plugin_name, '_wpgp_gist_token', '1234');

        $this->mock_github_client
            ->shouldReceive('authenticate')
            ->once();
        $pager = Mockery::mock('overload:Github\ResultPager');
        $pager->shouldReceive('fetchAll')
              ->with($this->mock_github_client, 'all')
              ->andReturn(array(
                  array('id' => 'first', 'code' => 'echo $truth;'),
                  array('id' => 'second', 'code' => 'echo $lies;')
              ));

        $result = $this->gist->all();

        $this->assertCount(2, $result);
        $this->assertEquals('first', $result[0]);
        $this->assertEquals('second', $result[1]);
    }

    public function testGetShouldFailIfNotReady()
    {
        $this->assertFalse($this->gist->get('1234'));
        $this->assertEquals('No GitHub OAuth token found.', $this->gist->getError()->get_error_message());
    }

    public function testGetShouldFailOnApiFailure()
    {
        cmb2_update_option(\Gistpen::$plugin_name, '_wpgp_gist_token', '1234');

        $this->mock_github_client
            ->shouldReceive('authenticate')
            ->once();
        $this->mock_github_client
            ->shouldReceive('show')
            ->with('1234')
            ->andThrow(new RuntimeException('Some Error Occurred', 1234));

        $this->assertFalse($this->gist->get('1234'));
        $this->assertEquals('Some Error Occurred', $this->gist->getError()->get_error_message());
    }

    public function testGetShouldReturnArrayWithZipAndVersion()
    {
        $response = array(
            'history' => array(
                array('version' => '1234')
            ),
            'files'   => array(
                'test.php' => array(
                    'language' => 'PHP',
                ),
            ),
        );

        cmb2_update_option(\Gistpen::$plugin_name, '_wpgp_gist_token', '1234');
        $this->mock_github_client
            ->shouldReceive('authenticate')
            ->once()
            ->shouldReceive('show')
            ->with('1234')
            ->once()
            ->andReturn($response);
        $this->mock_adapter
            ->shouldReceive('build')
            ->times(3)
            ->andReturn($this->mock_adapter);
        $this->mock_adapter
            ->shouldReceive('by_gist')
            ->with($response)
            ->andReturn($this->mock_zip);
        $this->mock_adapter
            ->shouldReceive('by_gist')
            ->with($response['files']['test.php'])
            ->andReturn($this->mock_file);
        $this->mock_adapter
            ->shouldReceive('by_gist')
            ->with($response['files']['test.php']['language'])
            ->andReturn($this->mock_lang);
        $this->mock_zip
            ->shouldReceive('add_file')
            ->with($this->mock_file);
        $this->mock_file
            ->shouldReceive('set_language')
            ->with($this->mock_lang);

        $gist = $this->gist->get('1234');

        $this->assertSame($this->mock_zip, $gist['zip']);
        $this->assertEquals('1234', $gist['version']);
    }

    public function tearDown()
    {
        parent::tearDown();
    }
}
