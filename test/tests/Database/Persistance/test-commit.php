<?php

use Intraxia\Gistpen\Database\Persistance\Commit as Persistance;
use Intraxia\Gistpen\Database\Query\Commit as CommitQuery;
use Intraxia\Gistpen\Database\Query\Head as HeadQuery;
use Intraxia\Gistpen\Facade\Adapter;

/**
 * @group  database
 */
class Persistance_Commit_Test extends \Intraxia\Gistpen\Test\UnitTestCase {

	function setUp() {
		parent::setUp();

		$this->_setRole( 'administrator' );
		$this->create_post_and_children();

		$this->persistance = new Persistance();
		$this->head_query = new HeadQuery();

		$this->zip = $this->head_query->by_id( $this->gistpen->ID );
	}

	function save_first_commit() {
		$this->ids = array(
			'zip'   => $this->gistpen->ID,
			'files' => $this->files,
		);

		$this->persistance->by_ids( $this->ids );
	}

	function test_should_not_save_commit_no_files() {
		$ids = array(
			'zip'   => $this->gistpen->ID,
		);

		$result = $this->persistance->by_ids( $ids );

		$this->assertNotInstanceOf( 'WP_Error', $result );
		$this->assertCount( 0, wp_get_post_revisions( $this->gistpen->ID ) );
	}

	function test_should_always_save_first_commit_with_right_data() {
		$ids = array(
			'zip'   => $this->gistpen->ID,
			'files' => $this->files,
		);

		$result = $this->persistance->by_ids( $ids );

		$this->assertNotInstanceOf( 'WP_Error', $result );

		$revisions = wp_get_post_revisions( $this->gistpen->ID );
		$this->assertCount( 1, $revisions );

		$last_revision = array_shift( $revisions );

		$meta = get_metadata( 'post', $last_revision->ID, '_wpgp_commit_meta', true );

		$this->assertInternalType( 'array', $meta['state_ids'] );
		$this->assertCount( 3, $meta['state_ids'] );
	}

	function test_should_not_save_commit_no_changes() {
		$this->save_first_commit();

		$result = $this->persistance->by_ids( $this->ids );

		$this->assertNotInstanceOf( 'WP_Error', $result );
		$revisions = wp_get_post_revisions( $this->gistpen->ID );
		$this->assertCount( 1, $revisions );
	}

	function test_should_save_commit_when_only_description_changed() {
		$this->save_first_commit();
		wp_update_post( array(
			'ID'         => $this->gistpen->ID,
			'post_title' => 'New description',
		) );

		$result = $this->persistance->by_ids( $this->ids );

		$this->assertNotInstanceOf( 'WP_Error', $result );

		$revisions = wp_get_post_revisions( $this->gistpen->ID );
		$this->assertCount( 2, $revisions );

		$last_revision = array_shift( $revisions );

		$meta = get_metadata( 'post', $last_revision->ID, '_wpgp_commit_meta', true );

		$this->assertInternalType( 'array', $meta['state_ids'] );
		$this->assertCount( 3, $meta['state_ids'] );
	}

	function test_should_save_commit_when_file_changed() {
		$this->save_first_commit();
		wp_update_post( array(
			'ID'          => $this->files[0],
			'post_content' => 'New content',
		) );

		$result = $this->persistance->by_ids( $this->ids );

		$this->assertNotInstanceOf( 'WP_Error', $result );

		$revisions = wp_get_post_revisions( $this->gistpen->ID );
		$this->assertCount( 2, $revisions );

		$last_revision = array_shift( $revisions );

		$meta = get_metadata( 'post', $last_revision->ID, '_wpgp_commit_meta', true );

		$this->assertInternalType( 'array', $meta['state_ids'] );
		$this->assertCount( 3, $meta['state_ids'] );
	}

	function test_should_save_commit_when_file_deleted() {
		$this->save_first_commit();
		wp_update_post( array(
			'ID'          => $this->ids['files'][0],
			'post_status' => 'inherit',
		) );
		$this->ids['deleted'] = array( $this->ids['files'][0] );
		unset( $this->ids['files'][0] );

		$result = $this->persistance->by_ids( $this->ids );

		$this->assertNotInstanceOf( 'WP_Error', $result );

		$revisions = wp_get_post_revisions( $this->gistpen->ID );
		$this->assertCount( 2, $revisions );

		$last_revision = array_shift( $revisions );

		$meta = get_metadata( 'post', $last_revision->ID, '_wpgp_commit_meta', true );

		$this->assertInternalType( 'array', $meta['state_ids'] );
		$this->assertCount( 3, $meta['state_ids'] );
	}

	function tearDown() {
		parent::tearDown();
	}
}
