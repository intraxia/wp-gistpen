<?php

namespace Intraxia\Gistpen\Http;

use Intraxia\Gistpen\Contract\Job;
use Intraxia\Gistpen\Jobs\Manager as Jobs;
use Intraxia\Gistpen\Model\Klass;
use Intraxia\Jaxion\Contract\Axolotl\EntityManager;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Jobs http controller.
 */
class JobsController {
	/**
	 * Jobs service.
	 *
	 * @var Jobs
	 */
	private $jobs;

	/**
	 * EntityManager service.
	 *
	 * @var EntityManager
	 */
	private $em;

	/**
	 * JobsController constructor.
	 *
	 * @param Jobs          $jobs
	 * @param EntityManager $em
	 */
	public function __construct( Jobs $jobs, EntityManager $em ) {
		$this->jobs = $jobs;
		$this->em   = $em;
	}

	/**
	 * Returns the registered jobs status & description.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function registered() {
		return new WP_REST_Response( array_values( $this->jobs->serialize() ), 200 );
	}

	/**
	 * Gets the status of a registered background job or run.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function status( WP_REST_Request $request ) {
		$name = $request->get_param( 'name' );
		$job  = $this->jobs->get( $name );

		if ( ! $job ) {
			return new WP_Error(
				'invalid_job',
				sprintf(
					__( 'Provided job %s is invalid.', 'wp-gistpen' ),
					$name
				),
				array( 'status' => 404 )
			);
		}

		$run_id = $request->get_param( 'run_id' );

		if ( ! $run_id ) {
			return new WP_REST_Response(
				array_merge( $job->serialize(), array( 'status' => $job->get_status() ) ),
				200
			);
		}

		$run = $job->fetch( $run_id );

		if ( is_wp_error( $run ) ) {
			$run->add_data( array( 'status' => 404 ) );

			return $run;
		}

		return new WP_REST_Response( $run->serialize(), 200 );
	}

	/**
	 * Dispatches a registered background job.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function dispatch( WP_REST_Request $request ) {
		$name = $request->get_param( 'name' );

		$job = $this->jobs->get( $name );

		if ( ! $job ) {
			return new WP_Error(
				'invalid_job',
				sprintf(
					__( 'Provided job %s is invalid.', 'wp-gistpen' ),
					$name
				),
				array( 'status' => 404 )
			);
		}

		$run = $job->dispatch();

		if ( is_wp_error( $run ) ) {
			$run->add_data( array( 'status' => 500 ) );

			return $run;
		}

		$response = new WP_REST_Response(
			array_merge( $job->serialize(), array( 'status' => $job->get_status() ) ),
			200
		);
		$response->header( 'Location', $run->rest_url );

		return $response;
	}

	/**
	 * Continues the job's processing.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function process( WP_REST_Request $request ) {
		$name = $request->get_param( 'name' );

		$job = $this->jobs->get( $name );

		if ( ! $job ) {
			return new WP_Error(
				'invalid_job',
				sprintf(
					__( 'Provided job %s is invalid.', 'wp-gistpen' ),
					$name
				),
				array( 'status' => 404 )
			);
		}

		$result = $job->process();

		if ( is_wp_error( $result ) ) {
			$result->add_data( array( 'status' => 500 ) );

			return $result;
		}

		return new WP_REST_Response( array( 'status' => $job->get_status() ), 200 );
	}

	/**
	 * Get a list of all the runs for a given job.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function runs( WP_REST_Request $request ) {
		$name = $request->get_param( 'name' );

		$job = $this->jobs->get( $name );

		if ( ! $job ) {
			return new WP_Error(
				'invalid_job',
				sprintf(
					__( 'Provided job %s is invalid.', 'wp-gistpen' ),
					$name
				),
				array( 'status' => 404 )
			);
		}

		return new WP_REST_Response( $job->runs()->serialize() );
	}

	/**
	 * Retrieves the console output for a registered
	 * background job's given run.
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function console( WP_REST_Request $request ) {
		$name = $request->get_param( 'name' );

		$job = $this->jobs->get( $name );

		if ( ! $job ) {
			return new WP_Error(
				'invalid_job',
				sprintf(
					__( 'Provided job %s is invalid.', 'wp-gistpen' ),
					$name
				),
				array( 'status' => 404 )
			);
		}

		$run_id = $request->get_param( 'run_id' );

		$run = $job->fetch( $run_id );

		if ( is_wp_error( $run ) ) {
			$run->add_data( array( 'status' => 404 ) );

			return $run;
		}

		$messages = $this->em->find_by( Klass::MESSAGE, array(
			'run_id' => $run_id,
		) );

		return new WP_REST_Response( array(
			'status' => $run->status,
			'messages' => $messages->serialize(),
		) );
	}
}
