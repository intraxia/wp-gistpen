<?php
namespace Intraxia\Gistpen\Listener;

use Intraxia\Gistpen\Jobs\Manager;
use Intraxia\Gistpen\Model\Klass;
use Intraxia\Gistpen\Model\Repo;
use Intraxia\Jaxion\Axolotl\Collection;
use Intraxia\Jaxion\Contract\Core\HasActions;

/**
 * Service to sync a Repo with Gist.
 */
class Sync implements HasActions {

	/**
	 * Jobs Manager service.
	 *
	 * @var Manager
	 */
	private $jobs;

	/**
	 * Database constructor.
	 *
	 * @param Manager $jobs
	 */
	public function __construct( Manager $jobs ) {
		$this->jobs = $jobs;
	}

	/**
	 * Export the provided Repo to Gist if the repo has sync enabled.
	 *
	 * @param Repo $repo
	 */
	public function export_repo( Repo $repo ) {
		if ( $repo->sync === 'on' ) {
			$this->jobs->get( 'export' )->dispatch( new Collection( Klass::REPO, array( $repo ) ) );
		}
	}

	/**
	 * Provides the array of actions the class wants to register with WordPress.
	 *
	 * These actions are retrieved by the Loader class and used to register the
	 * correct service methods with WordPress.
	 *
	 * @return array[]
	 */
	public function action_hooks() {
		return array(
			array(
				'hook'     => 'wpgp.create.repo',
				'method'   => 'export_repo',
				'priority' => 20,
			),
			array(
				'hook'     => 'wpgp.persist.repo',
				'method'   => 'export_repo',
				'priority' => 20,
			),
		);
	}
}
