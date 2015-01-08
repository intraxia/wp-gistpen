<?php
namespace WP_Gistpen\Model\Commit;

use WP_Gistpen\Model\Commit\State;

/**
 * Data object for an individual Commit
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Meta extends \WP_Gistpen\Model\Zip {

	/**
	 * Head ID
	 *
	 * @var int
	 * @since    0.5.0
	 */
	protected $head_id = null;

	/**
	 * Gist ID for Head Zip
	 *
	 * @var   string
	 * @since 0.5.0
	 */
	protected $head_gist_id = 'none';

	/**
	 * Array of File States
	 *
	 * @var array
	 * @since 0.5.0
	 */
	protected $states = array();

	/**
	 * Get the Head Zip ID for the Commit
	 *
	 * @return int Head Zip ID
	 * @since 0.5.0
	 */
	public function get_head_id() {
		return $this->head_id;
	}

	/**
	 * Validate & set the Head Zip ID for the Commit
	 *
	 * @param int    $head_id     Head Zip ID ID
	 * @since 0.5.0
	 */
	public function set_head_id( $head_id ) {
		$this->head_id = (int) $head_id;
	}

	/**
	 * Get the Head Zip's Gist ID for the Commit
	 *
	 * @return string     Head Zip's Gist ID
	 * @since 0.5.0
	 */
	public function get_head_gist_id() {
		return $this->head_gist_id;
	}

	/**
	 * Validate & set the Head Zip's Gist ID for the Commit
	 *
	 * @param int $head_gist_id Head Zip's Gist ID ID
	 * @since 0.5.0
	 */
	public function set_head_gist_id( $head_gist_id ) {
		$this->head_gist_id = $head_gist_id;
	}

	/**
	 * Get the Array of States
	 *
	 * @return array Array of states
	 * @since  0.5.0
	 */
	public function get_states() {
		return $this->states;
	}

	/**
	 * Validate and add a State to the Commit
	 *
	 * @param State $state State model object
	 * @throws Exception If not a State model object
	 * @since 0.5.0
	 */
	public function add_state( $state ) {
		if ( ! $state instanceof State ) {
			throw new Exception( 'State objects only added to states array' );
		}

		$state_id = $state->get_ID();

		if ( null !== $state_id ) {
			$this->states[ $state_id ] = $state;
		} else {
			$this->states[] = $state;
		}
	}
}
