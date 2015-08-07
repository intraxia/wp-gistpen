<?php
namespace Intraxia\Gistpen\Collection;

use Intraxia\Gistpen\Model\Commit\Meta as Commit;

/**
 * Collection object that holds the commit history
 *
 * @package    Intraxia\Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class History implements \Countable {

	/**
	 * ID of the head zip
	 *
	 * @var   int
	 * @since 0.5.0
	 */
	protected $head_id;

	/**
	 * Array of Commits in the History
	 *
	 * @var   \Gistpen\Model\Commit\Meta[]
	 * @since 0.5.0
	 */
	protected $commits = array();

	/**
	 * Returns the number of Commits in this History
	 *
	 * Implements the Countable interface
	 *
	 * @return int Number of Commits
	 * @since 0.5.0
	 */
	public function count() {
		return count( $this->commits );
	}

	/**
	 * Get the History's Head Zip ID
	 *
	 * @return int History's Zip ID
	 */
	public function get_head_id() {
		return $this->head_id;
	}

	/**
	 * Validate & set the History's Head ID
	 *
	 * @param int $head_id History's Head ID
	 */
	public function set_head_id( $head_id ) {
		$this->head_id = (int) $head_id;
	}

	/**
	 * Get the History's array of Commits
	 *
	 * @return \Gistpen\Model\Commit\Meta[]
	 * @since  0.5.0
	 */
	public function get_commits() {
		return $this->commits;
	}

	/**
	 * Set the array of commits in the History
	 *
	 * @param \Gistpen\Model\Commit\Meta[] $commits
	 * @since 0.5.0
	 */
	public function set_commits( $commits ) {
		$this->commits = array();

		foreach ( $commits as $id => $commit ) {
			$this->add_commit( $commit );
		}
	}

	/**
	 * Validate and add a Commit to the History
	 *
	 * @param Commit $commit
	 * @throws \Exception
	 * @since 0.5.0
	 */
	public function add_commit( $commit ) {
		if ( ! $commit instanceof Commit ) {
			throw new \Exception( 'Commit objects only added to Commits' );
		}

		$commit_id = $commit->get_ID();

		if ( null !== $commit_id ) {
			$this->commits[ $commit_id ] = $commit;
		} else {
			$this->commits[] = $commit;
		}
	}

	/**
	 * Retrieve the first commit in the history
	 *
	 * @return Commit History's first commit
	 */
	public function get_first_commit() {
		$first_commit_id = '';

		foreach ( $this->commits as $commit_id => $commit ) {
			if ( empty( $first_commit_id ) ) {
				$first_commit_id = $commit_id;
			} elseif ( $commit_id < $first_commit_id ) {
				$first_commit_id = $commit_id;
			}
		}

		$first_commit = $this->commits[ $first_commit_id ];

		return $first_commit;
	}

	/**
	 * Retrieve the last commit in the history
	 *
	 * @return Commit History's last commit
	 */
	public function get_last_commit() {
		$last_commit_id = '';

		foreach ( $this->commits as $commit_id => $commit ) {
			if ( empty( $last_commit_id ) ) {
				$last_commit_id = $commit_id;
			} elseif ( $commit_id > $last_commit_id ) {
				$last_commit_id = $commit_id;
			}
		}

		$last_commit = $this->commits[ $last_commit_id ];

		return $last_commit;
	}
}
