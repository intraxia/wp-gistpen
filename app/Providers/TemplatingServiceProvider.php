<?php
namespace Intraxia\Gistpen\Providers;

use Handlebars\Handlebars;
use Handlebars\Loader\FilesystemLoader;
use Intraxia\Jaxion\Contract\Core\Container;
use Intraxia\Jaxion\Contract\Core\ServiceProvider;
use Intraxia\Gistpen\Templating\Handlebars as HandlebarsTemplating;

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
			return new HandlebarsTemplating( new Handlebars( array(
				'loader' => new FilesystemLoader( $container->fetch( 'path' ) . 'client', array(
					'extension' => 'hbs',
				) )
			) ) );
		});
	}
}
