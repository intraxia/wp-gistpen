<?php
namespace Intraxia\Gistpen\Contract;

use Intraxia\Gistpen\Jobs\Status;
use Intraxia\Gistpen\Model\Run;
use Intraxia\Jaxion\Axolotl\Collection;
use Intraxia\Jaxion\Contract\Axolotl\Serializes;
use WP_Error;

interface Job extends Serializes {

	/**
	 * Get the current status of the job.
	 *
	 * @return Status
	 */
	public function get_status();

	/**
	 * Dispatch a job to process the provided data.
	 *
	 * Will process all possible data if nothing is provided.
	 *
	 * @param Collection|null $items
	 *
	 * @return Run|WP_Error
	 */
	public function dispatch( Collection $items = null );

	/**
	 * Fetches a Run by its run id.
	 *
	 * @param int $run_id
	 *
	 * @return Run|WP_Error
	 */
	public function fetch( $run_id );

	/**
	 * Process a batch of data.
	 *
	 * @return mixed
	 */
	public function process();

	/**
	 * Get the Collection of runs for the job.
	 *
	 * @return Collection
	 */
	public function runs();

	/**
	 * Get an individual run for the job.
	 *
	 * @param int $run_id
	 *
	 * @return Run
	 */
	public function run( $run_id );

	/**
	 * Get the Collection of messages for a given run.
	 *
	 * @param int $run_id
	 *
	 * @return Collection
	 */
	public function messages( $run_id );
}
