<?php
namespace WP_Gistpen\Collection;

use WP_Gistpen\Model\Commit\Meta as Commit;

/**
 * Collection object that holds the commit history
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class History implements \Countable {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.5.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.5.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

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
	 * @var   array
	 * @since 0.5.0
	 */
	protected $commits = array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.5.0
	 * @var      string    $plugin_name       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

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
	 * @return array History's array of Commits
	 * @since  0.5.0
	 */
	public function get_commits() {
		return $this->commits;
	}

	/**
	 * Set the array of commits in the History
	 *
	 * @param array $commits Array of commits
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
