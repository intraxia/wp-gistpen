<?php

namespace Intraxia\Gistpen\Test\Unit\Database\EntityManager;

use Intraxia\Gistpen\Jobs\Level;
use Intraxia\Gistpen\Jobs\Status;
use Intraxia\Gistpen\Model\Message;
use Intraxia\Gistpen\Model\Run;
use Intraxia\Gistpen\Lifecycle;
use Intraxia\Gistpen\Test\Unit\TestCase;
use Intraxia\Jaxion\Axolotl\Collection;
use Intraxia\Jaxion\Contract\Axolotl\EntityManager;

class CustomTableTest extends TestCase {
	/**
	 * @var EntityManager
	 */
	protected $em;

	/**
	 * @var int
	 */
	protected $run_id;

	/**
	 * @var string
	 */
	protected $scheduled;

	/**
	 * @var string
	 */
	protected $started;

	/**
	 * @var string
	 */
	protected $finished;

	/**
	 * @var int
	 */
	protected $message_id;

	/**
	 * @var string
	 */
	protected $logged;

	public function setUp() {
		parent::setUp();

		global $wpdb;

		$this->em = $this->app->make( EntityManager::class );
		$this->app->make( Lifecycle::class )->activate();

		// @codingStandardsIgnoreLine
		$wpdb->insert(
			$this->em->make_table_name( Run::class ),
			array(
				'job'          => 'import',
				'status'       => Status::FINISHED,
				'scheduled_at' => $this->scheduled = current_time( 'mysql' ),
				'started_at'   => $this->started = current_time( 'mysql' ),
				'finished_at'  => $this->finished = current_time( 'mysql' ),
			)
		);

		$this->run_id = $wpdb->insert_id;

		// @codingStandardsIgnoreLine
		$wpdb->insert(
			$this->em->make_table_name( Message::class ),
			array(
				'run_id'    => $this->run_id,
				'text'      => 'Run completed successfully.',
				'level'     => Level::SUCCESS,
				'logged_at' => $this->logged = current_time( 'mysql' ),
			)
		);

		$this->message_id = $wpdb->insert_id;
	}

	public function test_should_return_error_for_invalid_run_id() {
		$run = $this->em->find( Run::class, $this->run_id + 1 );

		$this->assertInstanceOf( 'WP_Error', $run );
	}

	public function test_should_return_error_for_invalid_message_id() {
		$run = $this->em->find( Message::class, $this->message_id + 1 );

		$this->assertInstanceOf( 'WP_Error', $run );
	}

	public function test_should_return_run_by_id() {
		/** @var Run $run */
		$run = $this->em->find( Run::class, $this->run_id );

		$this->assertInstanceOf( Run::class, $run );
		$this->assertEquals( 'import', $run->job );
		$this->assertEquals( Status::FINISHED, $run->status );
		$this->assertEquals( $this->scheduled, $run->scheduled_at );
		$this->assertEquals( $this->started, $run->started_at );
		$this->assertEquals( $this->finished, $run->finished_at );
	}

	public function test_should_return_message_by_id() {
		/** @var Message $message */
		$message = $this->em->find( Message::class, $this->message_id );

		$this->assertInstanceOf( Message::class, $message );
		$this->assertEquals( $this->run_id, $message->run_id );
		$this->assertEquals( 'Run completed successfully.', $message->text );
		$this->assertEquals( Level::SUCCESS, $message->level );
		$this->assertEquals( $this->logged, $message->logged_at );
	}

	public function test_should_return_list_of_all_runs() {
		$runs = $this->em->find_by( Run::class );

		$this->assertCount( 1, $runs );

		$run = $runs->first();

		$this->assertInstanceOf( Run::class, $run );
	}

	public function test_should_return_list_of_all_messages() {
		$messages = $this->em->find_by( Message::class );

		$this->assertCount( 1, $messages );

		$run = $messages->first();

		$this->assertInstanceOf( Message::class, $run );
	}

	public function test_should_return_list_of_runs_by_job() {
		$runs = $this->em->find_by( Run::class, array(
			'job' => 'import',
		) );

		$this->assertCount( 1, $runs );

		$run = $runs->first();

		$this->assertInstanceOf( Run::class, $run );
	}

	public function test_should_return_empty_list_of_runs_by_unknown_job() {
		$runs = $this->em->find_by( Run::class, array(
			'job' => 'unknown',
		) );

		$this->assertCount( 0, $runs );
	}

	public function test_should_return_list_of_messages_by_run_id() {
		$messages = $this->em->find_by( Message::class, array(
			'run_id' => $this->run_id,
		) );

		$this->assertCount( 1, $messages );

		$message = $messages->first();

		$this->assertInstanceOf( Message::class, $message );
	}

	public function test_should_return_empty_list_of_messages_by_unknown_run() {
		$messages = $this->em->find_by( Message::class, array(
			'run_id' => $this->run_id + 1,
		) );

		$this->assertCount( 0, $messages );
	}

	public function test_should_create_new_run() {
		global $wpdb;
		$data = array(
			'job'          => 'import',
			'status'       => Status::FINISHED,
			'scheduled_at' => current_time( 'mysql' ),
			'started_at'   => current_time( 'mysql' ),
			'finished_at'  => current_time( 'mysql' ),
		);
		/** @var Run $run */
		$run = $this->em->create( Run::class, $data );

		$this->assertInstanceOf( Run::class, $run );

		$this->assertEquals( $wpdb->insert_id, $run->ID );
		$this->assertEquals( $data['job'], $run->job );
		$this->assertEquals( $data['status'], $run->status );
		$this->assertEquals( $data['scheduled_at'], $run->scheduled_at );
		$this->assertEquals( $data['started_at'], $run->started_at );
		$this->assertEquals( $data['finished_at'], $run->finished_at );
	}

	public function test_should_create_new_message() {
		global $wpdb;
		$data = array(
			'run_id'    => $this->run_id,
			'text'      => 'Finished run successfully.',
			'level'     => Level::SUCCESS,
			'logged_at' => current_time( 'mysql' ),
		);
		/** @var Message $message */
		$message = $this->em->create( Message::class, $data );

		$this->assertInstanceOf( Message::class, $message );

		$this->assertEquals( $wpdb->insert_id, $message->ID );
		$this->assertEquals( $data['run_id'], $message->run_id );
		$this->assertEquals( $data['text'], $message->text );
		$this->assertEquals( $data['level'], $message->level );
		$this->assertEquals( $data['logged_at'], $message->logged_at );
	}

	public function test_should_not_create_new_message_with_no_repo_id() {
		$message = $this->em->create( Message::class, array(
			'text'      => 'Finished run successfully.',
			'level'     => Level::SUCCESS,
			'logged_at' => current_time( 'mysql' ),
		) );

		$this->assertInstanceOf( 'WP_Error', $message );
	}

	public function test_should_not_create_new_message_with_invalid_repo_id() {
		$message = $this->em->create( Message::class, array(
			'run_id'    => $this->run_id + 1,
			'text'      => 'Finished run successfully.',
			'level'     => Level::SUCCESS,
			'logged_at' => current_time( 'mysql' ),
		) );

		$this->assertInstanceOf( 'WP_Error', $message );
	}

	public function test_should_persist_new_run_without_primary_id() {
		global $wpdb;
		$data = array(
			'job'          => 'import',
			'status'       => Status::FINISHED,
			'scheduled_at' => current_time( 'mysql' ),
			'started_at'   => current_time( 'mysql' ),
			'finished_at'  => current_time( 'mysql' ),
		);
		$run  = new Run( $data );

		$run = $this->em->persist( $run );

		$this->assertInstanceOf( Run::class, $run );

		$this->assertEquals( $wpdb->insert_id, $run->ID );
		$this->assertEquals( $data['job'], $run->job );
		$this->assertEquals( $data['status'], $run->status );
		$this->assertEquals( $data['scheduled_at'], $run->scheduled_at );
		$this->assertEquals( $data['started_at'], $run->started_at );
		$this->assertEquals( $data['finished_at'], $run->finished_at );
	}

	public function test_should_persist_new_message_without_primary_id() {
		global $wpdb;
		$data    = array(
			'run_id'    => $this->run_id,
			'text'      => 'Another successful run.',
			'level'     => Level::SUCCESS,
			'logged_at' => current_time( 'mysql' ),
		);
		$message = new Message( $data );

		$message = $this->em->persist( $message );

		$this->assertInstanceOf( Message::class, $message );

		$this->assertEquals( $wpdb->insert_id, $message->ID );
		$this->assertEquals( $data['run_id'], $message->run_id );
		$this->assertEquals( $data['text'], $message->text );
		$this->assertEquals( $data['level'], $message->level );
		$this->assertEquals( $data['logged_at'], $message->logged_at );
	}

	public function test_should_not_persist_new_message_with_no_run_id() {
		$data    = array(
			'text'      => 'Another successful run.',
			'level'     => Level::SUCCESS,
			'logged_at' => current_time( 'mysql' ),
		);
		$message = new Message( $data );

		$message = $this->em->persist( $message );

		$this->assertInstanceOf( 'WP_Error', $message );
	}

	public function test_should_not_persist_new_message_with_invalid_run_id() {
		$data    = array(
			'run_id'    => $this->run_id + 1,
			'text'      => 'Another successful run.',
			'level'     => Level::SUCCESS,
			'logged_at' => current_time( 'mysql' ),
		);
		$message = new Message( $data );

		$message = $this->em->persist( $message );

		$this->assertInstanceOf( 'WP_Error', $message );
	}

	public function test_should_update_existing_run_with_primary_id() {
		/** @var Run $run */
		$run = $this->em->find( Run::class, $this->run_id );

		$run->job        = 'export';
		$run->status     = Status::RUNNING;
		$items           = $run->items = new Collection( 'string', array( 'hello' ) );
		$this->scheduled = $run->scheduled_at = current_time( 'mysql' );
		$this->started   = $run->started_at = current_time( 'mysql' );
		$this->finished  = $run->finished_at = current_time( 'mysql' );

		$run = $this->em->persist( $run );

		$this->assertInstanceOf( Run::class, $run );
		$this->assertEquals( 'export', $run->job );
		$this->assertEquals( Status::RUNNING, $run->status );
		$this->assertEquals( $items, $run->items );
		$this->assertEquals( $this->scheduled, $run->scheduled_at );
		$this->assertEquals( $this->started, $run->started_at );
		$this->assertEquals( $this->finished, $run->finished_at );
	}

	public function test_should_update_existing_message_with_primary_id() {
		/** @var Message $message */
		$message = $this->em->find( Message::class, $this->message_id );

		$text           = $message->text = 'New text for message';
		$message->level = Level::DEBUG;
		$this->logged   = $message->logged_at = current_time( 'mysql' );

		$message = $this->em->persist( $message );

		$this->assertInstanceOf( Message::class, $message );

		$this->assertEquals( $text, $message->text );
		$this->assertEquals( Level::DEBUG, $message->level );
		$this->assertEquals( $this->logged, $message->logged_at );
	}

	public function test_should_not_update_existing_message_with_no_run_id() {
		/** @var Message $message */
		$message = $this->em->find( Message::class, $this->message_id );

		$message->run_id = null;

		$message = $this->em->persist( $message );

		$this->assertInstanceOf( 'WP_Error', $message );
	}

	public function test_should_not_update_existing_message_with_invalid_run_id() {
		/** @var Message $message */
		$message = $this->em->find( Message::class, $this->message_id );

		$message->run_id = $this->run_id + 1;

		$message = $this->em->persist( $message );

		$this->assertInstanceOf( 'WP_Error', $message );
	}

	public function test_should_delete_existing_run_and_all_messages() {
		$run = $this->em->find( Run::class, $this->run_id );

		$this->em->delete( $run );

		$runs = $this->em->find_by( Run::class );

		$this->assertCount( 0, $runs );

		$messages = $this->em->find_by( Message::class );

		$this->assertCount( 0, $messages );
	}
}
