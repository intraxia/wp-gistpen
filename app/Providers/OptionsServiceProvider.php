<?php
namespace Intraxia\Gistpen\Providers;

use Intraxia\Gistpen\Options\User;
use Intraxia\Jaxion\Contract\Core\Container;
use Intraxia\Jaxion\Contract\Core\ServiceProvider;

class OptionsServiceProvider implements ServiceProvider {
	/**
	 * {@inheritDoc}
	 *
	 * @param Container $container
	 */
	public function register( Container $container ) {
		$container->define( 'options.user', function() {
			return new User;
		} );
	}
}
