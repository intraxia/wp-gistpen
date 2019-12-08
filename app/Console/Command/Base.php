<?php
namespace Intraxia\Gistpen\Console\Command;

use Intraxia\Jaxion\Contract\Axolotl\EntityManager;
use WP_CLI;

/**
 * Base command with shared functionality for commands.
 */
abstract class Base {

	/**
	 * Read post content from file or STDIN.
	 *
	 * @param string $arg Supplied argument.
	 * @return string
	 */
	protected function read_from_file_or_stdin( $arg ) {
		if ( '-' !== $arg ) {
			$readfile = $arg;
			if ( ! file_exists( $readfile ) || ! is_file( $readfile ) ) {
				WP_CLI::error( "Unable to read content from '{$readfile}'." );
			}
		} else {
			$readfile = 'php://stdin';
		}
		return file_get_contents( $readfile ); // phpcs:ignore
	}

	/**
	 * Get the patch result from the input arguments.
	 *
	 * @param  array $args
	 * @param  array $assoc_args
	 * @param  array $defaults
	 * @return array
	 */
	protected function get_patch_from_args( $args, $assoc_args, $defaults ) {
		if ( ! isset( $args[0] ) ) {
			$value = WP_CLI::get_value_from_arg_or_stdin( $args, -1 );
			$patch = WP_CLI::read_value( $value, $assoc_args );
		} else {
			$key    = $args[0];
			$subkey = null;

			if ( strpos( $key, '.' ) !== false ) {
				list( $key, $subkey ) = explode( '.', $args[0] );
			}

			if ( array_key_exists( $key, $defaults ) ) {
				$value = WP_CLI::get_value_from_arg_or_stdin( $args, 1 );
				$value = WP_CLI::read_value( $value, $assoc_args );
				$patch = [ $key => null === $subkey ? $value : [ $subkey => $value ] ];
			} else {
				$patch = WP_CLI::read_value( $args[0], $assoc_args );
			}
		}

		return $patch;
	}
}
