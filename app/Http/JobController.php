<?php
namespace Intraxia\Gistpen\Http;

use WP_Error;
use WP_REST_Response;

class JobController {
	/**
	 * Returns the registered jobs status and any queued jobs.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function registered() {
		return $this->not_implemented();
	}

	/**
	 * Dispatches one or all of registered background jobs.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function dispatch() {
		return $this->not_implemented();
	}

	/**
	 * Gets the status of a registered background job.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function status() {
		return $this->not_implemented();
	}

	/**
	 * Retrieves the console output for a registered
	 * background job's given run.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function console() {
		return $this->not_implemented();
	}

	/**
	 * Runs the job's next batch for the given data.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function next() {
		return $this->not_implemented();
	}

	/**
	 * Returns an error indicating jobs haven't been implemented yet.
	 *
	 * @return WP_Error
	 */
	protected function not_implemented() {
		return new WP_Error(
			'not_implemented',
			__( 'Background jobs have not been implemented', 'wp-gistpen' ),
			array( 'status' => 501 )
		);
	}
}
