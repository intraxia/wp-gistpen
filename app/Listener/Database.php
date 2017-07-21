<?php
namespace Intraxia\Gistpen\Listener;

use Intraxia\Gistpen\Database\EntityManager as EM;
use Intraxia\Gistpen\Model\Blob;
use Intraxia\Gistpen\Model\Commit;
use Intraxia\Gistpen\Model\Repo;
use Intraxia\Gistpen\Model\State;
use Intraxia\Jaxion\Axolotl\Collection;
use Intraxia\Jaxion\Contract\Axolotl\EntityManager;
use Intraxia\Jaxion\Contract\Core\HasActions;

class Database implements HasActions {

	/**
	 * Database service.
	 *
	 * @var EntityManager
	 */
	private $em;

	/**
	 * Database constructor.
	 *
	 * @param EntityManager $em
	 */
	public function __construct( EntityManager $em ) {
		$this->em = $em;
	}

	/**
	 * Checks if the Repo has changed and creates a new commit if it has.
	 *
	 * @param Repo $repo
	 */
	public function add_commit( Repo $repo ) {
		$commits = $this->em->find_by( EM::COMMIT_CLASS, array(
			'repo_id' => $repo->ID,
			'with'    => array(
				'states' => array(
					'with' => 'language'
				),
			),
			'orderby' => 'date',
			'order'   => 'DESC',
		) );

		if ( $commits->count() > 0 && $this->matches_last_commit( $repo, $commits->first() ) ) {
			return;
		}

		$new_commit = new Commit( array(
			'repo_id'     => $repo->ID,
			'description' => $repo->description,
		) );

		// This is the first commit.
		if ( $commits->count() === 0 ) {
			$states = new Collection( EM::STATE_CLASS );

			foreach ( $repo->blobs as $blob ) {
				$states = $states->add( $this->blob_to_state( $blob ) );
			}

			$new_commit->set_attribute( 'states', $states );

			$this->em->persist( $new_commit );

			return;
		}

		/** @var Commit $prev_commit */
		$prev_commit = $commits->first();

		// Get all the blobs that don't have a matching state in the previous commit.
		// These blobs are new.
		$added_states = $repo->blobs->filter( function ( Blob $blob ) use ( $prev_commit ) {
			return ! $prev_commit->states->contains( function ( State $state ) use ( $blob ) {
				return $state->blob_id === $blob->ID;
			} );
		} )
			// Save the blob's state.
			->map( function ( Blob $blob ) {
				return $this->em->persist( $this->blob_to_state( $blob ) );
			} );

		// Remove all the states that don't match an existing blob.
		$states = $prev_commit->states->filter( function ( State $state ) use ( $repo ) {
			return $repo->blobs->contains( function ( Blob $blob ) use ( $state ) {
				return $state->blob_id === $blob->ID;
			} );
		} )
			->map( function ( State $state ) use ( $repo ) {
				/** @var Blob $blob */
				$blob = $repo->blobs->find( function ( Blob $blob ) use ( $state ) {
					return $state->blob_id === $blob->ID;
				} );

				switch ( true ) {
					// Create a new state for blobs that have changed.
					case $blob->filename !== $state->filename :
					case $blob->code !== $state->code :
					case $blob->language->slug !== $state->language->slug :
						return $this->em->persist( $this->blob_to_state( $blob ) );
					// Otherwise, keep it.
					default:
						return $state;
				}
			} )->merge( $added_states );

		$new_commit->state_ids = $states->map( function ( State $state ) {
			return $state->ID;
		} )->to_array();

		$this->em->persist( $new_commit );
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
				'hook'   => 'wpgp.create.repo',
				'method' => 'add_commit',
			),
			array(
				'hook'   => 'wpgp.persist.repo',
				'method' => 'add_commit',
			),
		);
	}

	/**
	 * Matches up the commit and repo and determines whether it has changed.
	 *
	 * @param Repo   $repo
	 * @param Commit $commit
	 *
	 * @return bool
	 */
	private function matches_last_commit( Repo $repo, Commit $commit ) {
		if ( $repo->description !== $commit->description ) {
			return false;
		}

		if ( $repo->blobs->count() !== $commit->states->count() ) {
			return false;
		}

		$has_changed_blobs = $repo->blobs->filter( function ( Blob $blob ) use ( $commit ) {
				return ! $commit->states->contains( function ( State $state ) use ( $blob ) {
					if ( $state->blob_id === $blob->ID &&
					     $blob->filename === $state->filename &&
					     $blob->code === $state->code &&
					     $blob->language->slug === $state->language->slug
					) {
						return true;
					}

					return false;
				} );
			} )->count() > 0;

		if ( $has_changed_blobs ) {
			return false;
		}

		return true;
	}

	/**
	 * Map the Blob to a matching state.
	 *
	 * @param Blob $blob
	 *
	 * @return State
	 */
	private function blob_to_state( Blob $blob ) {
		return new State( array(
			'blob_id'  => $blob->ID,
			'filename' => $blob->filename,
			'code'     => $blob->code,
			'language' => $blob->language,
		) );
	}
}
