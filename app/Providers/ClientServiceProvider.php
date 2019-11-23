<?php
namespace Intraxia\Gistpen\Providers;

use Intraxia\Gistpen\Client\Gist;
use Intraxia\Jaxion\Contract\Core\Container;
use Intraxia\Jaxion\Contract\Core\ServiceProvider;
use Requests_Session;

/**
 * {@inheritDoc}
 */
class ClientServiceProvider implements ServiceProvider {

	/**
	 * Register the provider's services on the container.
	 *
	 * This method is passed the container to register on, giving the service provider
	 * an opportunity to register its services on the container in an encapsulated way.
	 *
	 * @param Container $container
	 */
	public function register( Container $container ) {
		$container->define( 'client.gist', function( Container $container ) {
			return new Gist( $container->fetch( 'options.site' ), new Requests_Session );
		} );
	}
}
