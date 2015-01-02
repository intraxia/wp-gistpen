<?php
use WP_Gistpen\Adapter\Gist as GistAdapter;

/**
 * @group adapters
 */
class WP_Gistpen_Adapter_Gist_Test extends WP_Gistpen_UnitTestCase {

	function setUp() {
		parent::setUp();

		$this->adapter = new GistAdapter( WP_Gistpen::$plugin_name, WP_Gistpen::$version );

		$this->mock_commit
			->shouldReceive( 'get_description' )
			->once()
			->andReturn( 'Post title 1' );
	}

	function test_create_gist_by_commit_public() {
		$this->mock_commit
			->shouldReceive( 'get_status' )
			->once()
			->andReturn( 'publish' )
			->shouldReceive( 'get_states')
			->once()
			->andReturn( array(
				$this->mock_state
			) );
		$this->mock_state
			->shouldReceive( 'get_gist_id' )
			->once()
			->andReturn( 'gist-filename' )
			->shouldReceive( 'get_code' )
			->once()
			->andReturn( 'echo $stuff;' );

		$gist = $this->adapter->create_by_commit( $this->mock_commit );

		$this->assertEquals( 'Post title 1', $gist['description'] );
		$this->assertTrue( $gist['public'] );
		$this->assertCount( 1, $gist['files'] );

		foreach ( $gist['files'] as $filename => $data ) {
			$this->assertEquals( 'gist-filename', $filename );
			$this->assertEquals( 'echo $stuff;', $data['content'] );
		}
	}

	function test_create_gist_by_commit_private() {
		$this->mock_commit
			->shouldReceive( 'get_status' )
			->once()
			->andReturn( 'draft' )
			->shouldReceive( 'get_states')
			->once()
			->andReturn( array(
				$this->mock_state
			) );
		$this->mock_state
			->shouldReceive( 'get_gist_id' )
			->once()
			->andReturn( 'gist-filename' )
			->shouldReceive( 'get_code' )
			->once()
			->andReturn( 'echo $stuff;' );

		$gist = $this->adapter->create_by_commit( $this->mock_commit );

		$this->assertEquals( 'Post title 1', $gist['description'] );
		$this->assertFalse( $gist['public'] );
		$this->assertCount( 1, $gist['files'] );

		foreach ( $gist['files'] as $filename => $data ) {
			$this->assertContains( 'gist-filename', $filename );
			$this->assertContains( 'echo $stuff;', $data['content'] );
		}
	}

	function test_update_gist_by_commit() {
		$this->mock_commit
			->shouldReceive( 'get_status' )
			->once()
			->andReturn( 'publish' )
			->shouldReceive( 'get_states')
			->once()
			->andReturn( array(
				$this->mock_state
			) );
		$this->mock_state
			->shouldReceive( 'get_gist_id' )
			->once()
			->andReturn( 'old-filename' )
			->shouldReceive( 'get_filename' )
			->once()
			->andReturn( 'new-filename' )
			->shouldReceive( 'get_code' )
			->once()
			->andReturn( 'echo $stuff;' )
			->shouldReceive( 'get_status' )
			->once()
			->andReturn( 'updated' );

		$gist = $this->adapter->update_by_commit( $this->mock_commit );

		$this->assertEquals( 'Post title 1', $gist['description'] );
		$this->assertTrue( $gist['public'] );
		$this->assertCount( 1, $gist['files'] );

		$filenames = array_keys( $gist['files'] );
		$filename = array_pop( $filenames );
		$datas = array_values( $gist['files'] );
		$data = array_pop( $datas );

		$this->assertEquals( 'old-filename', $filename );
		$this->assertArrayHasKey( 'content', $data );
		$this->assertArrayHasKey( 'filename', $data );
	}

	function test_update_gist_by_commit_add_file() {
		$this->mock_commit
			->shouldReceive( 'get_status' )
			->once()
			->andReturn( 'publish' )
			->shouldReceive( 'get_states')
			->once()
			->andReturn( array(
				$this->mock_state,
				$this->mock_state,
			) );
		$this->mock_state
			->shouldReceive( 'get_gist_id' )
			->once()
			->andReturn( 'old-filename' )
			->shouldReceive( 'get_filename' )
			->twice()
			->andReturnValues( array( 'new-filename', 'old-filename-2' ) )
			->shouldReceive( 'get_code' )
			->twice()
			->andReturnValues( array( 'echo $stuff;', 'echo $new_stuff;' ) )
			->shouldReceive( 'get_status' )
			->twice()
			->andReturnValues( array( 'updated', 'new' ) );

		$gist = $this->adapter->update_by_commit( $this->mock_commit );

		$this->assertEquals( 'Post title 1', $gist['description'] );
		$this->assertTrue( $gist['public'] );
		$this->assertCount( 2, $gist['files'] );

		$i = 1;

		foreach ( $gist['files'] as $filename => $data ) {
			$this->assertStringStartsWith( 'old-filename', $filename );
			$this->assertArrayHasKey( 'content', $data );

			if ( 1 === $i ) {
				$this->assertArrayHasKey( 'filename', $data );
			} else {
				$this->assertArrayNotHasKey( 'filename', $data );
			}

			$i++;
		}
	}

	function test_update_gist_by_commit_delete_file() {
		$this->mock_commit
			->shouldReceive( 'get_status' )
			->once()
			->andReturn( 'publish' )
			->shouldReceive( 'get_states')
			->once()
			->andReturn( array(
				$this->mock_state,
				$this->mock_state,
			) );
		$this->mock_state
			->shouldReceive( 'get_gist_id' )
			->twice()
			->andReturnValues( array( 'old-filename', 'old-filename-2' ) )
			->shouldReceive( 'get_filename' )
			->once()
			->andReturn( 'new-filename' )
			->shouldReceive( 'get_code' )
			->once()
			->andReturnValues( array( 'echo $stuff;', 'echo $new_stuff;' ) )
			->shouldReceive( 'get_status' )
			->twice()
			->andReturnValues( array( 'updated', 'deleted' ) );

		$gist = $this->adapter->update_by_commit( $this->mock_commit );

		$this->assertEquals( 'Post title 1', $gist['description'] );
		$this->assertTrue( $gist['public'] );
		$this->assertCount( 2, $gist['files'] );

		$i = 1;

		foreach ( $gist['files'] as $filename => $data ) {
			$this->assertStringStartsWith( 'old-filename', $filename );

			if ( 1 === $i ) {
				$this->assertArrayHasKey( 'filename', $data );
				$this->assertArrayHasKey( 'content', $data );
			} else {
				$this->assertNull( $data );
			}

			$i++;
		}
	}

	function tearDown() {
		parent::tearDown();
	}
}
