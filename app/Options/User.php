<?php
namespace Intraxia\Gistpen\Options;

use InvalidArgumentException;

/**
 * Class User
 *
 * User options service for managing the user options.
 *
 * @package    Intraxia\Gistpen
 * @subpackage Options
 */
class User {
	/**
	 * Registered options.
	 *
	 * @var array
	 */
	public static $defaults = array(
		'editor' => array(
			'theme'              => 'default',
			'invisibles_enabled' => 'off',
			'tabs_enabled'       => 'off',
			'indent_width'       => '2',
		),
	);

	/**
	 * Legacy user values to map to new values.
	 *
	 * @var array
	 */
	protected $legacy = array(
		'ace_theme'      => 'editor.theme',
		'ace_invisibles' => 'editor.invisibles_enabled',
		'ace_tabs'       => 'editor.tabs_enabled',
		'ace_width'      => 'editor.indent_width',
	);

	/**
	 * Options prefix.
	 *
	 * @var string
	 */
	protected $prefix = 'wpgp';

	/**
	 * Retrieve all the options for the provided user.
	 *
	 * @param  number $user_id
	 * @return array
	 */
	public function all( $user_id ) {
		$options = get_user_meta( $user_id, 'wpgp_options', true ) ?: static::$defaults;

		foreach ( $this->legacy as $legacy => $path ) {
			$value = get_user_meta( $user_id, $this->make_option( $legacy ), true );

			if ( $value ) {
				$options = $this->set_by_path( $options, $value, $path );

				delete_user_meta( $user_id, $this->make_option( $legacy ) );
				update_user_meta( $user_id, 'wpgp_options', $options );
			}
		}

		return $options;
	}

	/**
	 * Retrieves the option value for the current user.
	 *
	 * @param  number $user_id
	 * @param string $name
	 *
	 * @return mixed
	 *
	 * @throws InvalidArgumentException
	 */
	public function get( $user_id, $name ) {
		$value = $this->all( $user_id );
		$parts = explode( '.', $name );

		// @codingStandardsIgnoreLine
		while ( $part = array_shift( $parts ) ) {
			if ( ! isset( $value[ $part ] ) ) {
				throw new InvalidArgumentException( $name );
			}

			$value = $value[ $part ];
		}

		return $value;
	}

	/**
	 * Sets the option value for the current user.
	 *
	 * @param  number $user_id
	 * @param string $path
	 * @param string $value
	 *
	 * @return array Updated options.
	 *
	 * @throws InvalidArgumentException
	 */
	public function set( $user_id, $path, $value ) {
		$options = $this->set_by_path( $this->all( $user_id ), $value, $path );

		update_user_meta( $user_id, 'wpgp_options', $options );

		return $options;
	}

	/**
	 * Patch the options with the provided array.
	 *
	 * @param  number $user_id
	 * @param array  $update
	 *
	 * @return array
	 *
	 * @throws InvalidArgumentException
	 */
	public function patch( $user_id, array $update ) {
		$options = $this->all( $user_id );

		foreach ( $update as $key => $value ) {
			if ( ! isset( $options[ $key ] ) ) {
				throw new InvalidArgumentException( $key );
			}

			if ( ! is_array( $value ) ) {
				throw new InvalidArgumentException( $key );
			}

			$updating = $options[ $key ];

			foreach ( $value as $next_key => $next_value ) {
				if ( ! isset( $updating[ $next_key ] ) ) {
					throw new InvalidArgumentException( $key );
				}

				$updating[ $next_key ] = $next_value;
			}

			$options[ $key ] = $updating;
		}

		update_user_meta( $user_id, 'wpgp_options', $options );

		return $options;
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

	/**
	 * Updates the value for the provided array by the given
	 * dot-deliminted path, e.g. editor.theme will update
	 * $options['editor']['theme'] = $value.
	 *
	 * @param array  $options Options array to update.
	 * @param mixed  $value   Value to update with.
	 * @param string $path    Path to update.
	 *
	 * @return array Updated options array.
	 * @throws InvalidArgumentException
	 */
	private function set_by_path( $options, $value, $path ) {
		$exploded = explode( '.', $path );

		$temp = &$options;
		foreach ( $exploded as $key ) {
			if ( ! isset( $temp[ $key ] ) ) {
				throw new InvalidArgumentException( $path );
			}

			$temp = &$temp[ $key ];
		}
		$temp = $value;
		unset( $temp );

		return $options;
	}
}
