<?php
namespace Intraxia\Gistpen\Options;

use InvalidArgumentException;

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
	 * @inheritDoc
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
			$option['prism']['line-numbers']    = isset( $option['line-numbers'] ) && 'on' === $option['line-numbers'] ? true : false;
			$option['prism']['show-invisibles'] = isset( $option['show-invisibles'] ) && 'on' === $option['show-invisibles'] ? true : false;
		}

		if ( current_user_can( 'manage_options' ) ) {
			$priv = get_option( $this->slug . '_priv' );

			if ( ! $priv ) {
				$priv = array( 'gist' => array( 'token' => '' ) );
			}

			$option = array_merge( $option, $priv );
		}

		return $option;
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
			$option = get_option( $this->slug . '_no_priv' );

			foreach ( $patch['prism'] as $key => $value ) {
				if ( in_array( $key, array( 'line-numbers', 'show-invisibles' ) ) ) {
					$option['prism'][ $key ] = $value ? 'on' : 'off';
				}

				if ( 'theme' === $key ) {
					$option['prism'][ $key ] = $value;
				}
			}

			update_option( $this->slug . '_no_priv', $option );
		}

		if ( isset( $patch['gist'] ) ) {
			$option = get_option( $this->slug . '_priv' );

			foreach ( $patch['gist'] as $key => $value ) {
				if ( 'token' === $key ) {
					$option['gist'][ $key ] = $value;
				}
			}

			update_option( $this->slug . '_priv', $option );
		}
	}
}
