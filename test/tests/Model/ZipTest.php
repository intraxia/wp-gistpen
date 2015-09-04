<?php
namespace Intraxia\Gistpen\Test\Model;

use Intraxia\Gistpen\Database\Query;
use Intraxia\Gistpen\Model\Zip;
use Intraxia\Gistpen\Test\UnitTestCase;
use stdClass;

/**
 * @group models
 */
class ZipTest extends UnitTestCase
{
    /**
     * @var Zip
     */
    public $zip;

    public function setUp()
    {
        parent::setUp();
        $this->zip = new Zip();
    }

    public function testShouldConstructByArray()
    {
        $data = array(
            'description' => 'This zip',
            'status'      => 'publish',
            'ID'          => 123,
            'password'    => 'asdf',
            'gist_id'     => '12345',
        );

        $zip = new Zip($data);

        $this->assertEquals('This zip', $zip->get_description());
        $this->assertEquals('publish', $zip->get_status());
        $this->assertEquals(123, $zip->get_ID());
        $this->assertEquals('asdf', $zip->get_password());
        $this->assertEquals('12345', $zip->get_gist_id());
    }

    public function testShouldConstructWithExtraVars()
    {
        $data = array(
            'description' => 'This zip',
            'status'      => 'publish',
            'ID'          => 123,
            'password'    => 'asdf',
            'gist_id'     => '12345',
            'extra'       => 'Something irrelevant',
        );

        $zip = new Zip($data);

        $this->assertEquals('This zip', $zip->get_description());
        $this->assertEquals('publish', $zip->get_status());
        $this->assertEquals(123, $zip->get_ID());
        $this->assertEquals('asdf', $zip->get_password());
        $this->assertEquals('12345', $zip->get_gist_id());
    }

    public function testShouldConstructWithOnlyID()
    {
        $data = array(
            'ID' => 123
        );

        $zip = new Zip($data);

        $this->assertEquals('', $zip->get_description());
        $this->assertEquals('', $zip->get_status());
        $this->assertEquals(123, $zip->get_ID());
        $this->assertEquals('', $zip->get_password());
        $this->assertEquals('none', $zip->get_gist_id());
    }

    public function testShouldConstructWithOnlyDescription()
    {
        $data = array(
            'description' => 'This zip'
        );

        $zip = new Zip($data);

        $this->assertEquals('This zip', $zip->get_description());
        $this->assertEquals('', $zip->get_status());
        $this->assertEquals(null, $zip->get_ID());
        $this->assertEquals('', $zip->get_password());
        $this->assertEquals('none', $zip->get_gist_id());
    }

    public function testShouldConstructWithOnlyStatus()
    {
        $data = array(
            'status' => 'publish'
        );

        $zip = new Zip($data);

        $this->assertEquals('', $zip->get_description());
        $this->assertEquals('publish', $zip->get_status());
        $this->assertEquals(null, $zip->get_ID());
        $this->assertEquals('', $zip->get_password());
        $this->assertEquals('none', $zip->get_gist_id());
    }

    public function testShouldConstructWithOnlyPassword()
    {
        $data = array(
            'password' => 'asdf'
        );

        $zip = new Zip($data);

        $this->assertEquals('', $zip->get_description());
        $this->assertEquals('', $zip->get_status());
        $this->assertEquals(null, $zip->get_ID());
        $this->assertEquals('asdf', $zip->get_password());
        $this->assertEquals('none', $zip->get_gist_id());
    }

    public function testShouldConstructWithOnlyGistID()
    {
        $data = array(
            'gist_id' => '12345'
        );

        $zip = new Zip($data);

        $this->assertEquals('', $zip->get_description());
        $this->assertEquals('', $zip->get_status());
        $this->assertEquals(null, $zip->get_ID());
        $this->assertEquals('', $zip->get_password());
        $this->assertEquals('12345', $zip->get_gist_id());
    }

    public function testShouldConstructCompletelyByPost()
    {
        $post                = new \WP_Post(new stdClass);
        $post->post_title    = 'This zip';
        $post->post_status   = 'publish';
        $post->post_password = 'asdf';
        $post->ID            = 123;
        $post->gist_id       = '12345';
        $post->sync          = 'on';

        $zip = new Zip($post);

        $this->assertEquals('This zip', $zip->get_description());
        $this->assertEquals('publish', $zip->get_status());
        $this->assertEquals(123, $zip->get_ID());
        $this->assertEquals('asdf', $zip->get_password());
        $this->assertEquals('12345', $zip->get_gist_id());
    }

    public function testShouldGetAndSetDescription()
    {
        $this->zip->set_description('Post description');

        $this->assertEquals('Post description', $this->zip->get_description());
    }

    public function testShouldGetFiles()
    {
        $this->assertCount(0, $this->zip->get_files());
    }

    public function testShouldAddNewFileWithoutID()
    {
        $this->mock_file
            ->shouldReceive('get_ID')
            ->once();

        $this->zip->add_file($this->mock_file);
        $files = $this->zip->get_files();

        $this->assertTrue(isset($files[0]));
    }

    public function testShouldAddNewFileWithID()
    {
        $this->mock_file
            ->shouldReceive('get_ID')
            ->once()
            ->andReturn(123);

        $this->zip->add_file($this->mock_file);
        $files = $this->zip->get_files();

        $this->assertTrue(isset($files[123]));
    }

    public function testShouldAddMultipleFiles()
    {
        $this->mock_file
            ->shouldReceive('get_ID')
            ->twice()
            ->andReturn(null, 123);

        $this->zip->add_files(array(
            $this->mock_file,
            $this->mock_file
        ));
        $files = $this->zip->get_files();

        $this->assertTrue(isset($files[0]));
        $this->assertTrue(isset($files[123]));
    }

    public function testShouldGetAndSetID()
    {
        $this->zip->set_ID('123');

        $this->assertEquals(123, $this->zip->get_ID());
    }

    public function testShouldGetAndSetGistID()
    {
        $this->zip->set_gist_id('12345');

        $this->assertEquals('12345', $this->zip->get_gist_id());
    }

    public function testShouldSetSyncToOffIfNotOn()
    {
        $this->zip->set_sync('something');

        $this->assertEquals('off', $this->zip->get_sync());
    }

    public function testShouldGetAndSetCreateDate()
    {
        $this->zip->set_create_date('something');

        $this->assertEquals('something', $this->zip->get_create_date());
    }

    public function testShouldGetPostContentByFiles()
    {
        $this->mock_file
            ->shouldReceive('get_ID')
            ->once()
            ->andReturn(123)
            ->shouldReceive('get_post_content')
            ->once()
            ->andReturn('Post content');

        $this->zip->add_file($this->mock_file);

        $this->assertContains('Post content', $this->zip->get_post_content());
    }

    public function testShouldGetShortcodeContentByFiles()
    {
        $this->mock_file
            ->shouldReceive('get_ID')
            ->once()
            ->andReturn(123)
            ->shouldReceive('get_shortcode_content')
            ->once()
            ->andReturn('Shortcode content');

        $this->zip->add_file($this->mock_file);

        $this->assertContains('Shortcode content', $this->zip->get_shortcode_content());
    }

    public function tearDown()
    {
        parent::tearDown();
    }
}
