<?php
namespace Intraxia\Gistpen\Providers;

use Intraxia\Gistpen\Params\Globals;
use Intraxia\Gistpen\Params\Prism;
use Intraxia\Gistpen\Params\Repo;
use Intraxia\Gistpen\Params\Repository as Params;
use Intraxia\Jaxion\Contract\Core\Container;
use Intraxia\Jaxion\Contract\Core\ServiceProvider;

class ParamsServiceProvider implements ServiceProvider {

	/**
	 * @inheritDoc
	 */
	public function register( Container $container ) {
		$container->define( 'params', function() {
			return new Params();
		} );

		$container->define('params.globals', function( Container $container ) {
			return new Globals( $container->fetch( 'url' ) );
		} );

		$container->define('params.prism', function( Container $container ) {
			return new Prism( $container->fetch( 'options.site' ) );
		} );

		$container->define('params.repo', function( Container $container ) {
			return new Repo( $container->fetch( 'database' ) );
		} );
	}
}
