<?php
namespace Intraxia\Gistpen\Console\Command;

use Intraxia\Gistpen\Options\User as UserOptions;
use WP_CLI;
use WP_CLI\Fetchers\User as UserFetcher;
use function \WP_CLI\Utils\get_flag_value;

/**
 * Manages a user's gistpen configuration.
 *
 * ## EXAMPLES
 *
 *     # Set a new theme.
 *     $ wp gistpen user update editor.theme twilight
 *     Success: Updated prism.theme to twilight.
 */
class User extends Base {

	/**
	 * UserOptions service.
	 *
	 * @var UserOptions
	 */
	protected $options;

	/**
	 * UserOptions service.
	 *
	 * @var UserFetcher
	 */
	protected $fetcher;

	/**
	 * Constructor.
	 *
	 * @param UserOptions $options
	 * @param UserFetcher $fetcher
	 */
	public function __construct( UserOptions $options, UserFetcher $fetcher ) {
		$this->options = $options;
		$this->fetcher = $fetcher;
	}


	/**
	 * Get user config value.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The ID of the user. Can be an id, email, or login.
	 *
	 * [<key>]
	 * : The name of the meta field to get. Returns everything if omitted.
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
	 * @param  array $args
	 * @param  array $assoc_args
	 */
	public function get( $args, $assoc_args ) {
		$args    = $this->replace_login_with_user_id( $args );
		$user_id = $args[0];

		if ( isset( $args[1] ) ) {
			$value = $this->options->get( $user_id, $args[1] );
		} else {
			$value = $this->options->all( $user_id );
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
		$args    = $this->replace_login_with_user_id( $args );
		$user_id = array_shift( $args );

		$this->options->patch(
			$user_id,
			$this->get_patch_from_args( $args, $assoc_args, UserOptions::$defaults )
		);

		WP_CLI::success( __( 'Updated user options.', 'wp-gispen' ) );
	}
	/**
	 * Replaces user_login value with user ID.
	 *
	 * @param array $args
	 * @return array
	 */
	private function replace_login_with_user_id( $args ) {
		$user    = $this->fetcher->get_check( $args[0] );
		$args[0] = $user->ID;

		return $args;
	}
}
