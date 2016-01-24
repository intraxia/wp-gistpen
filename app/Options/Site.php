<?php
namespace Intraxia\Gistpen\Options;

use InvalidArgumentException;

class Site {
	/**
	 * Registered options.
	 *
	 * @var array
	 */
	protected $options = array( 'gist_token', 'gistpen_highlighter_theme', 'gistpen_line_number' );

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
			$all[ $option ] = cmb2_get_option( 'wp-gistpen', $this->make_option( $option ) );
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

		return cmb2_get_option( 'wp-gistpen', $this->make_option( $name ) );
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

		cmb2_update_option( 'wp-gistpen', $this->make_option( $name ), $value );
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
