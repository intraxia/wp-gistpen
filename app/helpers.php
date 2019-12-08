<?php
namespace Intraxia\Gistpen;

use Intraxia\Jaxion\Core\Loader;

/**
 * Returns the application container. Bootstraps it if it doesn't exist yet.
 *
 * @return \Di\Container    The application container.
 */
function container() {
	static $container;

	if ( ! $container ) {
		$builder = new \DI\ContainerBuilder();
		$builder->addDefinitions( dirname( __DIR__ ) . '/resources/config/container.php' );

		$container = $builder->build();
	}

	return $container;
}

/**
 * Boot the application.
 */
function boot() {
	add_action( 'plugins_loaded', [ container()->get( Loader::class ), 'run' ] );
}
