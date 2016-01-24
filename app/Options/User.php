<?php
namespace Intraxia\Gistpen\Options;

use InvalidArgumentException;

class User {
	/**
	 * Registered options.
	 *
	 * @var array
	 */
	protected $options = array( 'ace_theme' );

	/**
	 * Options prefix.
	 *
	 * @var string
	 */
	protected $prefix = 'wpgp';

	/**
	 * Retrieve all the options for the provided user.
	 *
	 * @return array
	 */
	public function all() {
		$all = array();

		foreach ( $this->options as $option ) {
			$all[ $option ] = get_user_meta( get_current_user_id(), $this->make_option( $option ), true );
		}

		return $all;
	}

	/**
	 * Retrieves the option value for the current user.
	 *
	 * @param string $name
	 *
	 * @return mixed
	 *
	 * @throws InvalidArgumentException
	 */
	public function get( $name ) {
		if ( ! in_array( $name, $this->options, true ) ) {
			throw new InvalidArgumentException( $name );
		}

		return get_user_meta( get_current_user_id(), $this->make_option( $name ), true );
	}

	/**
	 * Sets the option value for the current user.
	 *
	 * @param string $name
	 * @param string $value
	 *
	 * @throws InvalidArgumentException
	 */
	public function set( $name, $value ) {
		if ( ! in_array( $name, $this->options, true ) ) {
			throw new InvalidArgumentException( $name );
		}

		update_user_meta( get_current_user_id(), $this->make_option( $name ), $value );
	}

	/**
	 * Turns an option string into its WordPress option name.
	 *
	 * @param string $option
	 *
	 * @return string
	 */
	private function make_option( $option ) {
		return "_{$this->prefix}_{$option}";
	}
}
