<?php
namespace Intraxia\Gistpen\Providers;

use Intraxia\Gistpen\Listener\Database;
use Intraxia\Gistpen\Listener\Migration;
use Intraxia\Gistpen\Listener\Sync;
use Intraxia\Jaxion\Contract\Core\Container;
use Intraxia\Jaxion\Contract\Core\ServiceProvider;

/**
 * {@inheritDoc}
 */
class ListenerServiceProvider implements ServiceProvider {

	/**
	 * Register the provider's services on the container.
	 *
	 * This method is passed the container to register on, giving the service provider
	 * an opportunity to register its services on the container in an encapsulated way.
	 *
	 * @param Container $container
	 */
	public function register( Container $container ) {
		$container->define( 'listener.database', function( Container $container ) {
			return new Database( $container->fetch( 'database' ) );
		} );

		$container->define( 'listener.sync', function( Container $container ) {
			return new Sync( $container->fetch( 'jobs' ) );
		} );

		$container->define( 'listener.migration', function( Container $container ) {
			return new Migration(
				$container->fetch( 'database' ),
				'wpgp',
				$container->fetch( 'slug' ),
				$container->fetch( 'version' )
			);
		} );
	}
}
