<?php

namespace Intraxia\Gistpen\Jobs;

use Intraxia\Gistpen\Contract\Job;
use Intraxia\Gistpen\Model\Klass;
use Intraxia\Gistpen\Model\Message;
use Intraxia\Gistpen\Model\Run;
use Intraxia\Jaxion\Axolotl\Collection;
use Intraxia\Jaxion\Contract\Axolotl\EntityManager;
use WP_Error;

abstract class AbstractJob implements Job {
	/**
	 * EntityManager service.
	 *
	 * @var EntityManager
	 */
	protected $em;

	/**
	 * Run currently being processed.
	 *
	 * @var Run
	 */
	private $current_run;

	/**
	 * AbstractJob constructor.
	 *
	 * @param EntityManager $em
	 */
	public function __construct( EntityManager $em ) {
		$this->em = $em;
	}

	/**
	 * Get the Job's name.
	 *
	 * @return string
	 */
	abstract protected function name();

	/**
	 * Get the Job's slug.
	 *
	 * @return string
	 */
	abstract protected function slug();

	/**
	 * Get the Job's description.
	 *
	 * @return mixed
	 */
	abstract protected function description();

	/**
	 * Fetch all the items the Job can process.
	 *
	 * @return Collection|WP_Error
	 */
	abstract protected function fetch_items();

	/**
	 * Process a single item. Return the modified item for further
	 * processing.
	 *
	 * @param mixed $item Queue item to iterate over.
	 *
	 * @return mixed|null
	 */
	abstract protected function process_item( $item );

	/**
	 * {@inheritdoc}
	 *
	 * @param Collection|null $items
	 *
	 * @return Run|WP_Error
	 */
	public function dispatch( Collection $items = null ) {
		if ( null === $items ) {
			$items = $this->fetch_items();
		}

		if ( is_wp_error( $items ) ) {
			return $items;
		}

		if ( ! ( $items instanceof Collection ) ) {
			return new WP_Error(
				'invalid_items',
				sprintf(
					__( 'items passed into dispatch or returned by fetch_items for job %s is not a Collection' , 'wp-gistpen' ),
					$this->slug()
				)
			);
		}

		/** @var WP_Error|Run $run */
		$run = $this->em->create( Klass::RUN, array(
			'scheduled_at' => $this->make_timestamp(),
			'items'        => $items,
			'status'       => Status::SCHEDULED,
			'job'          => $this->slug(),
		) );

		if ( is_wp_error( $run ) ) {
			return $run;
		}

		$this->trigger();

		return $run;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return WP_Error|null
	 */
	public function process() {
		if ( $this->is_running() ) {
			return new WP_Error(
				'job_running',
				sprintf(
					__( 'Job %s is already running.', 'wp-gistpen' ),
					$this->slug()
				)
			);
		}

		$start_time = time();
		$this->set_status( Status::PROCESSING );

		do {
			$this->current_run = $this->get_next_run();

			if ( null === $this->current_run ) {
				break;
			}

			$this->start();

			$items = $this->current_run->items->to_array();

			foreach ( $items as $key => $item ) {
				$task = $this->process_item( $item );

				if ( $task ) {
					$items[ $key ] = $task;
				} else {
					unset( $items[ $key ] );
				}

				if ( $this->time_exceeded( $start_time ) || $this->memory_exceeded() ) {
					break;
				}
			}

			if ( $items ) {
				$this->pause( $items );
			} else {
				$this->finish();
			}
		} while ( ! $this->time_exceeded( $start_time ) && ! $this->memory_exceeded() );

		$this->set_status( Status::IDLE );
		$this->trigger();

		return null;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param $run_id
	 *
	 * @return Run|WP_Error
	 */
	public function fetch( $run_id ) {
		return $this->em->find( Klass::RUN, $run_id );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return Collection|WP_Error
	 */
	public function runs() {
		return $this->em->find_by( Klass::RUN, array(
			'job' => $this->slug(),
		) );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return Run|WP_Error
	 */
	public function run( $run_id ) {
		return $this->em->find( Klass::RUN, $run_id, array(
			'job' => $this->slug(),
		) );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return Collection|WP_Error
	 */
	public function messages( $run_id ) {
		return $this->em->find_by( Klass::MESSAGE, array(
			'run_id' => $run_id,
		) );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return string
	 */
	public function get_status() {
		return get_option( $this->make_status_key(), Status::IDLE );
	}

	/**
	 * Serializes the model's public data into an array.
	 *
	 * @return array
	 */
	public function serialize() {
		return array(
			'name'        => $this->name(),
			'slug'        => $this->slug(),
			'description' => $this->description(),
			'rest_url'    => rest_url( sprintf(
				'intraxia/v1/gistpen/jobs/%s',
				$this->slug()
			) ),
			'runs_url'    => rest_url( sprintf(
				'intraxia/v1/gistpen/jobs/%s/runs',
				$this->slug()
			) ),
		);
	}

	/**
	 * Log a new message to the database for the current run.
	 *
	 * @param string $msg
	 * @param string $lvl
	 *
	 * @return Message|WP_Error
	 */
	protected function log( $msg, $lvl = Level::INFO ) {
		return $this->em->create( Klass::MESSAGE, array(
			'run_id'    => $this->current_run->ID,
			'text'      => $msg,
			'level'     => $lvl,
			'logged_at' => $this->make_timestamp(),
		) );
	}

	/**
	 * Create a new timestamp for the current time for mysql.
	 *
	 * @return string
	 */
	protected function make_timestamp() {
		return current_time( 'mysql' );
	}

	/**
	 * Trigger a new job if the current job isn't running
	 * and another job is scheduled.
	 */
	private function trigger() {
		if ( ! $this->is_running() && $this->has_next_run() ) {
			wp_remote_post( $this->process_url(), $this->get_request_args() );
		}
	}

	/**
	 * Get the process URL for the job.
	 *
	 * @return string
	 */
	private function process_url() {
		return rest_url( sprintf(
			'intraxia/v1/gistpen/jobs/%s/process',
			$this->slug()
		) );
	}

	/**
	 * Get the request arguments required by the HTTP request.
	 *
	 * @return array
	 */
	private function get_request_args() {
		return array(
			'timeout'   => 0.01,
			'blocking'  => false,
			'cookies'   => $_COOKIE,
			'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
			'headers'   => array(
				'X-WP-Nonce' => wp_create_nonce( 'wp_rest' ),
			)
		);
	}

	/**
	 * Set the current status of the Job.
	 *
	 * @param $status
	 */
	private function set_status( $status ) {
		if ( Status::isValid( $status ) ) {
			update_option( $this->make_status_key(), $status, false );
		}
	}

	/**
	 * Create the key used by the status option.
	 *
	 * @return string
	 */
	private function make_status_key() {
		return "_wpgp_job_{$this->slug()}_status";
	}

	/**
	 * Determine whether the Job is currently running.
	 *
	 * @return bool
	 */
	private function is_running() {
		return $this->get_status() === Status::PROCESSING;
	}

	/**
	 * Update the database with the started run.
	 */
	private function start() {
		if ( ! $this->current_run->started_at ) {
			$this->current_run->started_at = current_time( 'mysql' );
		}

		$this->current_run->status = Status::RUNNING;

		$this->em->persist( $this->current_run );
	}

	/**
	 * Update the database with the remaining items for the paused run.
	 *
	 * @param array $items
	 */
	private function pause( $items ) {
		$this->current_run->items  = new Collection( $this->current_run->items->get_type(), $items );
		$this->current_run->status = Status::PAUSED;

		$this->em->persist( $this->current_run );
	}

	/**
	 * Update the database with the finished run.
	 */
	private function finish() {
		$this->current_run->status      = Status::FINISHED;
		$this->current_run->finished_at = current_time( 'mysql' );
		$this->current_run->items       = null;

		$this->em->persist( $this->current_run );
	}

	/**
	 * Determines whether the amount of time the batch job can
	 * run has been exceeded.
	 *
	 * @param int $start_time
	 *
	 * @return bool
	 */
	private function time_exceeded( $start_time ) {
		return time() >= $start_time + 20;
	}

	/**
	 * Determines whether the amount of memory used has
	 * exceeded the maximum amount allowed for the run.
	 *
	 * @return bool
	 */
	private function memory_exceeded() {
		$memory_limit   = $this->get_memory_limit() * 0.9; // 90% of max memory
		$current_memory = memory_get_usage( true );

		return $current_memory >= $memory_limit;
	}

	/**
	 * Determine the maximum amount of memory allowed
	 * for the run.
	 *
	 * @return int
	 */
	private function get_memory_limit() {
		if ( function_exists( 'ini_get' ) ) {
			$memory_limit = ini_get( 'memory_limit' );
		} else {
			// Sensible default.
			$memory_limit = '128M';
		}

		if ( ! $memory_limit || - 1 === $memory_limit ) {
			// Unlimited, set to 32GB.
			$memory_limit = '32000M';
		}

		return intval( $memory_limit ) * 1024 * 1024;
	}

	/**
	 * Retrieve the next run from the database.
	 *
	 * @return Run|null
	 */
	private function get_next_run() {
		$runs = $this->get_paused_or_scheduled_runs();

		if ( $runs->count() === 0 ) {
			return null;
		}

		return $runs->first();
	}

	/**
	 * Determine whether there are any more runs for the current job.
	 *
	 * @return bool
	 */
	private function has_next_run() {
		$runs = $this->get_paused_or_scheduled_runs();

		return $runs->count() > 0;
	}

	/**
	 * Get all the currently paused or scheduled runs.
	 *
	 * @return Collection|WP_Error
	 */
	private function get_paused_or_scheduled_runs() {
		$runs = $this->em->find_by( Klass::RUN, array(
			'status' => Status::PAUSED,
		) );

		if ( $runs->count() === 0 ) {
			$runs = $this->em->find_by( Klass::RUN, array(
				'status' => Status::SCHEDULED,
			) );
		}

		return $runs;
	}

}
