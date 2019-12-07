<?php
namespace Intraxia\Gistpen\Options;

use Intraxia\Gistpen\Params\Globals;
use InvalidArgumentException;

/**
 * Provides a simplified interface for
 * interacting with the site's plugin options.
 *
 * Constraints:
 *
 * 1. The boolean values should normalize into the database as "on" | "off".
 * 2. We should accept booleans or "on" | "off" as acceptable values.
 * 3. The boolean values should normalize out of the database as booleans.
 *
 * This makes for a very flexible API for use between the CLI & the API.
 *
 * @package Intraxia\Gistpen
 * @subpackage Options
 * @since 1.0.0
 */
class Site {

	/**
	 * Option defaults.
	 *
	 * @var array
	 */
	public static $defaults = [
		'prism' => [
			'theme'           => 'default',
			'line-numbers'    => false,
			'show-invisibles' => false,
		],
		'gist'  => [
			'token' => '',
		],
	];

	/**
	 * Plugin slug.
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 * Valid themes.
	 *
	 * @var array
	 */
	protected $themes;

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
		return array_merge( $this->fetch_prism(), $this->fetch_gist() );
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
		if ( ! array_key_exists( $name, static::$defaults ) ) {
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
	 * @throws InvalidArgumentException
	 */
	public function patch( array $patch ) {
		foreach ( $patch as $key => $value ) {
			if ( ! array_key_exists( $key, static::$defaults ) ) {
				throw new InvalidArgumentException( $key );
			}

			$option = $this->{"fetch_{$key}"}();

			foreach ( $patch[ $key ] as $subkey => $value ) {
				if ( ! array_key_exists( $subkey, static::$defaults[ $key ] ) ) {
					throw new InvalidArgumentException( $key . '.' . $subkey );
				}

				if ( 'theme' === $subkey && ! $this->is_valid_theme( $value ) ) {
					throw new InvalidArgumentException( $key . '.' . $subkey );
				}

				if ( 'token' === $subkey && ! is_string( $value ) ) {
					throw new InvalidArgumentException( $key . '.' . $subkey );
				}

				if ( in_array( $subkey, [ 'line-numbers', 'show-invisibles' ], true ) ) {
					if ( ! in_array( $value, [ 'on', 'off', true, false ], true ) ) {
						throw new InvalidArgumentException( $key . '.' . $subkey );
					}

					$value = true === $value || 'on' === $value ? 'on' : 'off';
				}

				$option[ $key ][ $subkey ] = $value;
			}

			$this->{"save_{$key}"}( $option );
		}
	}

	/**
	 * Retrieves all the unprivileged information from the options.
	 *
	 * @return array
	 */
	protected function fetch_prism() {
		$option = get_option( $this->slug . '_no_priv', [ 'prism' => static::$defaults['prism'] ] );

		if ( ! is_array( $option ) || ! isset( $option['prism'] ) ) {
			$option = [ 'prism' => [] ];
		}

		$option['prism']['theme']           =
			isset( $option['prism']['theme'] ) && $this->is_valid_theme( $option['prism']['theme'] )
				? $option['prism']['theme']
				: 'default';
		$option['prism']['line-numbers']    =
			isset( $option['prism']['line-numbers'] ) && (
				'on' === $option['prism']['line-numbers'] || true === $option['prism']['line-numbers']
			)
				? true
				: false;
		$option['prism']['show-invisibles'] =
			isset( $option['prism']['show-invisibles'] ) && (
				'on' === $option['prism']['show-invisibles'] || true === $option['prism']['show-invisibles']
			)
				? true
				: false;

		return $option;
	}

	/**
	 * Returns the privileged options.
	 *
	 * @return array
	 */
	protected function fetch_gist() {
		$option = get_option( $this->slug . '_priv', [ 'gist' => static::$defaults['gist'] ] );

		if ( ! $option ) {
			$option = [ 'gist' => [ 'token' => '' ] ];
		}

		// Noramlize gist token to empty string.
		$option['gist']['token'] = ! empty( $option['gist']['token'] ) ? $option['gist']['token'] : '';

		return $option;
	}

	/**
	 * Saves the unprivileged options.
	 *
	 * @param array $option
	 */
	protected function save_prism( array $option ) {
		update_option( $this->slug . '_no_priv', [
			'prism' => [
				'theme'           => ! empty( $option['prism']['theme'] )
						? $option['prism']['theme']
						: static::$defaults['prism']['theme'],
				'line-numbers'    => ! empty( $option['prism']['line-numbers'] )
						? $option['prism']['line-numbers']
						: static::$defaults['prism']['line-numbers'],
				'show-invisibles' => ! empty( $option['prism']['show-invisibles'] )
					? $option['prism']['show-invisibles']
					: static::$defaults['prism']['show-invisibles'],
			],
		] );
	}

	/**
	 * Saves the privileged options.
	 *
	 * @param array $option
	 */
	protected function save_gist( array $option ) {
		update_option( $this->slug . '_priv', $option );
	}

	/**
	 * Check if the theme is valid.
	 *
	 * @param  string $theme
	 * @return boolean
	 */
	protected function is_valid_theme( $theme ) {
		return in_array( $theme, Globals::$themes, true );
	}
}
