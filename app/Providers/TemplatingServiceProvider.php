<?php
namespace Intraxia\Gistpen\Providers;

use Intraxia\Jaxion\Contract\Core\Container;
use Intraxia\Jaxion\Contract\Core\ServiceProvider;
use Intraxia\Gistpen\Templating\File as FileTemplating;

class TemplatingServiceProvider implements ServiceProvider {

	/**
	 * {@inheritDoc}
	 *
	 * @param Container $container
	 *
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 */
	public function register( Container $container ) {
		$container->define( 'templating', function ( Container $container ) {
			return new FileTemplating(
				$container->fetch( 'config' ),
				$container->fetch( 'translations' ),
				$container->fetch( 'path' ) . 'views/'
			);
		} );
	}
}
