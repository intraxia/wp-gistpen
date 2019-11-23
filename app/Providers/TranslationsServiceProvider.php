<?php

namespace Intraxia\Gistpen\Providers;

use Intraxia\Jaxion\Contract\Core\Container;
use Intraxia\Jaxion\Contract\Core\ServiceProvider;
use Intraxia\Gistpen\View\Translations;

/**
 * {@inheritDoc}
 */
class TranslationsServiceProvider implements ServiceProvider {

	/**
	 * Register the provider's services on the container.
	 *
	 * This method is passed the container to register on, giving the service provider
	 * an opportunity to register its services on the container in an encapsulated way.
	 *
	 * @param Container $container
	 */
	public function register( Container $container ) {
		$container->share( 'translations', function () {
			return new Translations();
		} );
	}
}
