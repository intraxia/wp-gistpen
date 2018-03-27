<?php
namespace Intraxia\Gistpen\Providers;

use Intraxia\Gistpen\Config;
use Intraxia\Gistpen\ConfigType;
use Intraxia\Jaxion\Contract\Core\Container;
use Intraxia\Jaxion\Contract\Core\ServiceProvider;

class ConfigServiceProvider implements ServiceProvider {

	/**
	 * Register the provider's services on the container.
	 *
	 * This method is passed the container to register on, giving the service provider
	 * an opportunity to register its services on the container in an encapsulated way.
	 *
	 * @param Container $container
	 */
	public function register( Container $container ) {
		$container->share( 'config', function ( Container $container ) {
			return new Config(
				new ConfigType( ConfigType::PLUGIN ),
				$container->fetch( 'file' )
			);
		} );
	}
}
