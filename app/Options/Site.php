<?php
namespace Intraxia\Gistpen\Options;

use InvalidArgumentException;

/**
 * Provides a simplified interface for
 * interacting with the site's plugin options.
 *
 * @package Intraxia\Gistpen
 * @subpackage Options
 * @since 1.0.0
 */
class Site {
	/**
	 * Registered options.
	 *
	 * @var array
	 */
	protected $options = array( 'prism', 'gist' );

	/**
	 * Plugin slug.
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 * Constructor.
	 *
	 * @param string $slug
	 */
	public function __construct( $slug ) {
		$this->slug = $slug;
	}

	/**
	 * Retrieve all the options for the provided user.
	 *
	 * @return array
	 */
	public function all() {
		return array_merge( $this->fetch_no_priv(), $this->fetch_priv() );
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

		$options = $this->all();

		if ( isset( $options[ $name ] ) ) {
			return $options[ $name ];
		}

		return null;
	}

	/**
	 * Patches the current settings with the provided array.
	 *
	 * @param array $patch
	 */
	public function patch( array $patch ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( isset( $patch['prism'] ) ) {
			$option = $this->fetch_no_priv();

			foreach ( $patch['prism'] as $key => $value ) {
				if ( in_array( $key, array( 'theme', 'line-numbers', 'show-invisibles' ) ) ) {
					$option['prism'][ $key ] = $value;
				}
			}

			$this->save_no_priv( $option );
		}

		if ( isset( $patch['gist'] ) ) {
			$option = $this->fetch_priv();

			foreach ( $patch['gist'] as $key => $value ) {
				if ( 'token' === $key ) {
					$option['gist'][ $key ] = $value;
				}
			}

			$this->save_priv( $option );
		}
	}

	/**
	 * Retrieves all the unprivileged information from the options.
	 *
	 * @return array
	 */
	protected function fetch_no_priv() {
		$option = get_option( $this->slug . '_no_priv' );

		if ( ! $option ) {
			$option = array(
				'prism' => array(
					'theme'           => 'default',
					'line-numbers'    => false,
					'show-invisibles' => false,
				),
			);
		} else {
			if ( ! is_array( $option ) || ! isset( $option['prism'] ) ) {
				$option = array( 'prism' => array() );
			}

			$option['prism']['theme'] = isset( $option['prism']['theme'] ) ? $option['prism']['theme'] : 'default';
			$option['prism']['line-numbers']    = isset( $option['prism']['line-numbers'] ) && 'on' === $option['prism']['line-numbers'] ? true : false;
			$option['prism']['show-invisibles'] = isset( $option['prism']['show-invisibles'] ) && 'on' === $option['prism']['show-invisibles'] ? true : false;
		}

		return $option;
	}

	/**
	 * Returns the privileged options.
	 *
	 * @return array
	 */
	protected function fetch_priv() {
		$option = array();

		if ( current_user_can( 'manage_options' ) ) {
			$option = get_option( $this->slug . '_priv' );

			if ( ! $option ) {
				$option = array( 'gist' => array( 'token' => '' ) );
			}
		}

		return $option;
	}

	/**
	 * Saves the unprivileged options.
	 *
	 * @param array $option
	 */
	protected function save_no_priv( array $option ) {
		/**
		 * This actually never runs, given the flow
		 * in which this method is normally called,
		 * but it's kept here for ideological purity.
		 */
		// @codeCoverageIgnoreStart
		if ( ! isset( $option['prism'] ) ) {
			$option['prism'] = array();
		}
		// @codeCoverageIgnoreEnd

		$option['prism']['theme']           = ! empty( $option['prism']['theme'] ) ? $option['prism']['theme'] : 'default';
		$option['prism']['line-numbers']    = ! empty( $option['prism']['line-numbers'] ) ? 'on' : 'off';
		$option['prism']['show-invisibles'] = ! empty( $option['prism']['show-invisibles'] ) ? 'on' : 'off';

		update_option( $this->slug . '_no_priv', array( 'prism' => $option['prism'] ) );
	}

	/**
	 * Saves the privileged options.
	 *
	 * @param array $option
	 */
	protected function save_priv( array $option ) {
		update_option( $this->slug . '_priv', $option );
	}
}
