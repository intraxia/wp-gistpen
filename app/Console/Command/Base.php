<?php
namespace Intraxia\Gistpen\Console\Command;

use Intraxia\Jaxion\Contract\Axolotl\EntityManager;

/**
 * Base command with shared functionality for commands.
 */
abstract class Base {

	/**
	 * EntityManager service.
	 *
	 * @var EntityManager
	 */
	protected $em;

	/**
	 * Constructor.
	 *
	 * @param EntityManager $em
	 */
	public function __construct( EntityManager $em ) {
		$this->em = $em;
	}

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
}
