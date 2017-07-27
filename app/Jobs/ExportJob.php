<?php
namespace Intraxia\Gistpen\Jobs;

use Intraxia\Gistpen\Model\Klass;
use Intraxia\Gistpen\Model\Repo;
use Intraxia\Jaxion\Contract\Axolotl\Collection;
use WP_Error;

class ExportJob extends AbstractJob {

	/**
	 * {@inheritdoc}
	 *
	 * @return string
	 */
	protected function name() {
		return 'export';
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return string
	 */
	protected function description() {
		return 'Export all unexported gistpen repos.';
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return Collection|WP_Error
	 */
	protected function fetch_items() {
		return $this->em->find_by( Klass::REPO, array(
			'nopaging' => true,
		) );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param Repo $value
	 *
	 * @return null
	 */
	protected function process_item( $value ) {
		if ( ! ( $value instanceof Repo ) ) {
			$this->log( 'Expected to see instance of Repo, got ' . gettype( $value ) . ' instead.', Status::ERROR );

			return null;
		}

		$this->log( 'Successfully processed repo: ' . $value->ID, Level::SUCCESS );

		return null;
	}
}
