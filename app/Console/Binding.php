<?php
namespace Intraxia\Gistpen\Console;

use Intraxia\Jaxion\Contract\Core\HasActions;
use Intraxia\Jaxion\Contract\Core\AnnotatedActions;
use Intraxia\Jaxion\Core\Annotatable;
use Intraxia\Jaxion\Core\Annotation\Action;
use Psr\Container\ContainerInterface;
use WP_CLI;

/**
 * Console binding.
 */
class Binding implements HasActions {
	use Annotatable;

	/**
	 * Container instance to resolve commands from.
	 *
	 * @var ContainerInterface
	 */
	protected $container;

	/**
	 * List of commands to bind.
	 *
	 * @var array
	 */
	protected $commands = [
		'repo' => Command\Repo::class,
		'blob' => Command\Blob::class,
		'site' => Command\Site::class,
	];

	/**
	 * Constructor.
	 *
	 * @param ContainerInterface $container
	 */
	public function __construct( ContainerInterface $container ) {
		$this->container = $container;
	}

	/**
	 * Register the gistpen command with WP-CLI.
	 *
	 * @Action(hook="init")
	 */
	public function register_command() {
		if ( class_exists( WP_CLI::class ) ) {
			foreach ( $this->commands as $name => $command ) {
				WP_CLI::add_command(
					'gistpen ' . $name,
					$this->container->get( $command )
				);
			}
		}
	}
}
