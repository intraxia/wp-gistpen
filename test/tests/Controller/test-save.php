<?php
use WP_Gistpen\Facade\Database;
use WP_Gistpen\Controller\Save;

/**
 * @group controllers
 */
class WP_Gistpen_Controller_Save_Test extends WP_Gistpen_UnitTestCase {

	function setUp() {
		parent::setUp();

		$this->database = new Database();

		$this->save = new SaveTest();
		$this->save->set_database( $this->mock_database );

		$this->create_post_and_children();
		$this->_setRole( 'administrator' );
	}

	function test_update_fails_no_perms() {
		$this->_setRole( 'subscriber' );
		$data = array(
			'description'  => 'New Gistpen Description',
			'status'       => 'auto-draft',
			'ID'           => $this->gistpen->ID,
			'files'        => array(
				array(
					'slug'     => 'New Gistpen',
					'code'     => 'echo $stuff;',
					'ID'       => null,
					'language' => 'php',
				),
			),
		);

		$result = $this->save->update( $data );

		$this->assertInstanceOf( 'WP_Error', $result );
	}

	function test_update_fails_head_fails() {
		$this->mock_database
			->shouldReceive( 'persist' )
			->with( 'head' )
			->once()
			->andReturn( $this->mock_database )
			->shouldReceive( 'by_zip' )
			->once()
			->andReturn( new WP_Error );

		$data = array(
			'description'  => 'New Gistpen Description',
			'status'       => 'auto-draft',
			'ID'           => null,
			'files'        => array(
				array(
					'slug'     => 'New Gistpen',
					'code'     => 'echo $stuff;',
					'ID'       => null,
					'language' => 'php',
				),
			),
		);

		$result = $this->save->update( $data );

		$this->assertInstanceOf( 'WP_Error', $result );
	}

	function test_update_fails_commit_fails() {
		$this->mock_database
			->shouldReceive( 'persist' )
			->with( 'head' )
			->once()
			->andReturn( $this->mock_database )
			->shouldReceive( 'by_zip' )
			->once()
			->andReturn( array() )
			->shouldReceive( 'persist' )
			->with( 'commit' )
			->once()
			->andReturn( $this->mock_database )
			->shouldReceive( 'by_ids' )
			->once()
			->andReturn( new WP_Error );

		$data = array(
			'description'  => 'New Gistpen Description',
			'status'       => 'auto-draft',
			'ID'           => null,
			'files'        => array(
				array(
					'slug'     => 'New Gistpen',
					'code'     => 'echo $stuff;',
					'ID'       => null,
					'language' => 'php',
				),
			),
		);

		$result = $this->save->update( $data );

		$this->assertInstanceOf( 'WP_Error', $result );
	}

	function test_update_succeeds() {
		$this->mock_database
			->shouldReceive( 'persist' )
			->with( 'head' )
			->once()
			->andReturn( $this->mock_database )
			->shouldReceive( 'by_zip' )
			->once()
			->andReturn( array( 'zip' => 1 ) )
			->shouldReceive( 'persist' )
			->with( 'commit' )
			->once()
			->andReturn( $this->mock_database )
			->shouldReceive( 'by_ids' )
			->once()
			->andReturn( array() );

		$data = array(
			'description'  => 'New Gistpen Description',
			'status'       => 'auto-draft',
			'ID'           => null,
			'files'        => array(
				array(
					'slug'     => 'New Gistpen',
					'code'     => 'echo $stuff;',
					'ID'       => null,
					'language' => 'php',
				),
			),
		);

		$result = $this->save->update( $data );

		$this->assertInternalType( 'int', $result );
	}

	function test_status_stays_in_sync() {
		$zip = $this->database->query()->by_id( $this->gistpen->ID );

		wp_update_post( array(
			'ID' => $this->gistpen->ID,
			'post_status' => 'pending',
		) );

		$this->assertEquals( 'pending', get_post_status( $zip->get_ID() ) );

		$files = $zip->get_files();

		foreach ( $files as $file ) {
			$this->assertEquals( 'pending', get_post_status( $file->get_ID() ) );
		}

		wp_update_post( array(
			'ID' => $this->gistpen->ID,
			'post_status' => 'trash',
		) );

		$this->assertEquals( 'trash', get_post_status( $zip->get_ID() ) );

		$files = $zip->get_files();

		foreach ( $files as $file ) {
			$this->assertEquals( 'trash', get_post_status( $file->get_ID() ) );
		}
	}

	function test_delete_children() {
		wp_delete_post( $this->gistpen->ID, true );

		foreach ($this->files as $file_id => $value) {
			$this->assertEquals( null, get_post( $file_id ) );
		}
	}

	function tearDown() {
		parent::tearDown();
	}
}

class SaveTest extends Save {
	public function set_database( $database ) {
		$this->database = $database;
	}
}
