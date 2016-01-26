<?php
namespace Intraxia\Gistpen\Providers;

use Intraxia\Gistpen\App;
use Intraxia\Gistpen\Embed;
use Intraxia\Jaxion\Contract\Core\Container;
use Intraxia\Jaxion\Contract\Core\ServiceProvider;

class EmbedServiceProvider implements ServiceProvider {
	/**
	 * {@inheritDoc}
	 */
	public function register( Container $container ) {
		$container->share( array( 'embed' => 'Intraxia\Gistpen\Embed' ), function ( App $app ) {
			return new Embed( $app->fetch( 'facade.database' ), $app->fetch( 'assets' ), $app->fetch( 'path' ), $app->fetch( 'url' ) );
		} );
	}
}
