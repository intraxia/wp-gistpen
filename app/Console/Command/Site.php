<?php
namespace Intraxia\Gistpen\Console\Command;

use Intraxia\Jaxion\Contract\Axolotl\EntityManager;
use Intraxia\Gistpen\Model\Repo as RepoModel;
use Intraxia\Gistpen\Options\Site as SiteOptions;
use WP_CLI;
use function \WP_CLI\Utils\get_flag_value;
use WP_CLI\Entity\Utils as EntityUtils;

/**
 * Manages the gistpen's site configuration.
 *
 * ## EXAMPLES
 *
 *     # Set a new theme.
 *     $ wp gistpen site update prism.theme xonokai
 *     Success: Updated prism.theme to xonokai.
 */
class Site {

	/**
	 * Site options service.
	 *
	 * @var SiteOptions
	 */
	protected $options;

	/**
	 * Constructor.
	 *
	 * @param SiteOptions $options
	 */
	public function __construct( SiteOptions $options ) {
		$this->options = $options;
	}

	/**
	 * Gets the value for the site option.
	 *
	 * ## OPTIONS
	 *
	 * [<key>]
	 * : Key for the option. If omitted, returns all of the options. Can use
	 *     a dot separator to access nested values.
	 *
	 * [--format=<format>]
	 * : Get value in a particular format.
	 * ---
	 * default: var_export
	 * options:
	 *   - var_export
	 *   - json
	 *   - yaml
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     # Get all options.
	 *     $ wp gistpen site get
	 *     array (
	 *       'prism' =>
	 *       array (
	 *         'theme' => 'default',
	 *         'line-numbers' => false,
	 *         'show-invisibles' => false,
	 *       ),
	 *       'gist' =>
	 *       array (
	 *         'token' => 'none',
	 *       ),
	 *     )
	 *
	 *     # Get the prism settings in JSON.
	 *     $ wp gistpen site get prism --format=json
	 *     {"theme":"default","line-numbers":false,"show-invisibles":false}
	 *
	 *     # Get the prism theme.
	 *     $ wp gistpen site get prism.theme
	 *     default
	 *
	 * @param  array $args
	 * @param  array $assoc_args
	 */
	public function get( $args, $assoc_args ) {
		if ( ! isset( $args[0] ) ) {
			$value = $this->options->all();
		} else {
			$key    = $args[0];
			$subkey = null;

			if ( strpos( $key, '.' ) !== false ) {
				list( $key, $subkey ) = explode( '.', $args[0] );
			}

			if ( ! array_key_exists( $key, SiteOptions::$defaults ) ) {
				return WP_CLI::error( "\"{$key}\" is not a valid key." );
			}

			if (
				null !== $subkey && ! array_key_exists( $subkey, SiteOptions::$defaults[ $key ] )
			) {
				return WP_CLI::error( "\"{$key}.{$subkey}\" is not a valid key." );
			}

			$value = $this->options->get( $key );

			if ( null !== $subkey ) {
				$value = $value[ $subkey ];
			}
		}

		WP_CLI::print_value( $value, $assoc_args );
	}

	/**
	 * Updates an gistpen site option value.
	 *
	 * ## OPTIONS
	 *
	 * [<key>]
	 * : The name of the option to update.
	 *
	 * [<value>]
	 * : The new value. Boolean values must be either "on" or "off".
	 *
	 * [--format=<format>]
	 * : The serialization format for the value.
	 * ---
	 * default: plaintext
	 * options:
	 *   - plaintext
	 *   - json
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     # Set a new theme.
	 *     $ wp gistpen site update prism.theme xonokai
	 *     Success: Updated 'prism.theme' to xonokai.
	 *
	 * @param  array $args
	 * @param  array $assoc_args
	 */
	public function patch( $args, $assoc_args ) {
		if ( ! isset( $args[0] ) ) {
			$value = WP_CLI::get_value_from_arg_or_stdin( $args, -1 );
			$patch = WP_CLI::read_value( $value, $assoc_args );
		} else {
			$key    = $args[0];
			$subkey = null;

			if ( strpos( $key, '.' ) !== false ) {
				list( $key, $subkey ) = explode( '.', $args[0] );
			}

			if ( array_key_exists( $key, SiteOptions::$defaults ) ) {
				$value = WP_CLI::get_value_from_arg_or_stdin( $args, 1 );
				$value = WP_CLI::read_value( $value, $assoc_args );
				$patch = [ $key => null === $subkey ? $value : [ $subkey => $value ] ];
			} else {
				$patch = WP_CLI::read_value( $args[0], $assoc_args );
			}
		}

		$this->options->patch( $patch );

		WP_CLI::success( __( 'Updated site options.', 'wp-gispen' ) );
	}
}
