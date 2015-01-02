<?php
namespace WP_Gistpen\Model\Commit;

/**
 * This is the class description.
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class State extends \WP_Gistpen\Model\File {

	/**
	 * The state's status
	 *
	 * Can be:
	 * * 'new'
	 * * 'updated'
	 * * 'deleted'
	 *
	 * @var   string
	 * @since 0.5.0
	 */
	protected $status;

	/**
	 * Post ID for the State's Head File
	 *
	 * @var   int
	 * @since 0.5.0
	 */
	protected $head_id;

	/**
	 * Gist ID for the File
	 *
	 * Usually the filename of the previous Commit,
	 * as that's how Gist gets updated
	 *
	 * @var   string
	 * @since 0.5.0
	 */
	protected $gist_id;

	/**
	 * Get the State's change status
	 *
	 * @return string State's status
	 * @since  0.5.0
	 */
	public function get_status() {
		return $this->status;
	}

	/**
	 * Validate and set the State's change status
	 *
	 * @param string $status new State status
	 * @since 0.5.0
	 */
	public function set_status( $status ) {
		if ( ! in_array( $status, array( 'new', 'updated', 'deleted' ) ) ) {
			throw new Exception;
		}

		$this->status = $status;
	}

	/**
	 * Get the State's Head File ID
	 *
	 * @return int Head File ID
	 * @since  0.5.0
	 */
	public function get_head_id() {
		return $this->head_id;
	}

	/**
	 * Validate and set the State's Head ID
	 *
	 * @param int $head_id State's Head ID
	 * @since 0.5.0
	 */
	public function set_head_id( $head_id ) {
		$this->head_id = (int) $head_id;
	}

	/**
	 * Get the State's Gist ID
	 *
	 * @return string       Gist ID
	 */
	public function get_gist_id() {
		return $this->gist_id;
	}

	/**
	 * Get the State's Gist ID
	 *
	 * @return string Gist ID
	 * @since  0.5.0
	 */
	public function set_gist_id( $gist_id ) {
		$this->gist_id = $gist_id;
	}
}
