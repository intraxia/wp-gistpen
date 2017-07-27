<?php
namespace Intraxia\Gistpen\Jobs;

use Intraxia\Gistpen\Contract\Job;
use Intraxia\Jaxion\Axolotl\Collection;
use Intraxia\Jaxion\Axolotl\Dictionary;
use Intraxia\Jaxion\Contract\Axolotl\Serializes;

class Manager implements Serializes {
	/**
	 * Jobs collection.
	 *
	 * @var Collection
	 */
	protected $jobs;

	/**
	 * @inheritDoc
	 *
	 * @param Job[] $jobs
	 */
	public function __construct( array $jobs = array() ) {
		$this->jobs = new Dictionary( 'string', 'Intraxia\Gistpen\Contract\Job', $jobs );
	}

	/**
	 * Add a job to the job manager.
	 *
	 * @param string $alias
	 * @param Job    $job
	 *
	 * @return $this
	 */
	public function add_job( $alias, Job $job ) {
		$this->jobs = $this->jobs->add( $alias, $job );

		return $this;
	}

	/**
	 * Serializes the model's public data into an array.
	 *
	 * @return Job[]
	 */
	public function serialize() {
		return $this->jobs->serialize();
	}

	/**
	 * Get job by name.
	 *
	 * @param $alias
	 *
	 * @return Job|null
	 */
	public function get( $alias ) {
		return $this->jobs->get( $alias );
	}
}
