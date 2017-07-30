<?php

namespace Intraxia\Gistpen\Params;

use Intraxia\Gistpen\Jobs\Manager;
use Intraxia\Jaxion\Contract\Core\HasFilters;

class Jobs implements HasFilters {
	/**
	 * Jobs Manager service.
	 *
	 * @var Manager
	 */
	private $jobs;

	/**
	 * Jobs constructor.
	 *
	 * @param Manager $jobs
	 */
	public function __construct( Manager $jobs ) {
		$this->jobs = $jobs;
	}

	/**
	 * Add jobs key to params array.
	 *
	 * @param array $params Current params array.
	 *
	 * @return array
	 */
	public function apply_jobs( $params ) {
		$params['jobs'] = $this->jobs->serialize();
		$parts          = $params['route']['parts'];

		if ( $params['route']['name'] === 'jobs' && ! $parts->run && ! $parts->job ) {
			foreach ( $params['jobs'] as $key => $job ) {
				$params['jobs'][ $key ]['status'] = $this->jobs->get( $job['slug'] )->get_status();
			}
		}

		if ( $params['route']['name'] === 'jobs' && $parts->job ) {
			$params['jobs'][ $parts->job ]['status'] = $this->jobs->get( $parts->job )->get_status();
			$params['runs'] = $this->jobs->get( $parts->job )->runs()->serialize();

			if ( $parts->run ) {
				$params['runs'] = array( $this->jobs->get( $parts->job )->run( $parts->run )->serialize() );
				$params['messages'] = $this->jobs->get( $parts->job )->messages( $parts->run )->serialize();
			}
		}

		return $params;
	}

	public function apply_jobs_props( $params ) {
		$params = $this->apply_jobs( $params );
		$parts  = $params['route']['parts'];

		if ( $params['route']['name'] === 'jobs' && $parts->job ) {
			$job = $this->jobs->get( $parts->job );

			if ( ! $parts->run ) {
				$params['job']           = $job->serialize();
				$params['job']['status'] = $job->get_status();
				$params['job']['runs']   = $job->runs()->serialize();
			} else {
				$params['run'] = $job->run( $parts->run )->serialize();
				$params['run']['messages'] = $job->messages( $parts->run )->serialize();
			}

			if ( $parts->run && $parts->job ) {

			}
		}

		return $params;
	}

	/**
	 * @inheritDoc
	 */
	public function filter_hooks() {
		return array(
			array(
				'hook'   => 'params.state.settings',
				'method' => 'apply_jobs',
			),
			array(
				'hook'   => 'params.props.settings',
				'method' => 'apply_jobs_props',
			),
		);
	}
}
