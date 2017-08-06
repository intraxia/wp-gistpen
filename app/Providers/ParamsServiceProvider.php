<?php
namespace Intraxia\Gistpen\Providers;

use Intraxia\Gistpen\Params\Editor;
use Intraxia\Gistpen\Params\Gist;
use Intraxia\Gistpen\Params\Globals;
use Intraxia\Gistpen\Params\Jobs;
use Intraxia\Gistpen\Params\Prism;
use Intraxia\Gistpen\Params\Repo;
use Intraxia\Gistpen\Params\Repository as Params;
use Intraxia\Gistpen\Params\Route;
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
			return new Globals( $container->fetch( 'config' ) );
		} );

		$container->define('params.prism', function( Container $container ) {
			return new Prism( $container->fetch( 'options.site' ) );
		} );

		$container->define('params.gist', function( Container $container ) {
			return new Gist( $container->fetch( 'options.site' ) );
		} );

		$container->define('params.repo', function( Container $container ) {
			return new Repo( $container->fetch( 'database' ) );
		} );

		$container->define( 'params.route', function ( Container $container ) {
			return new Route();
		} );

		$container->define( 'params.jobs', function ( Container $container ) {
			return new Jobs( $container->fetch( 'jobs' ) );
		} );

		$container->define( 'params.editor', function ( Container $container ) {
			return new Editor(
				$container->fetch( 'config' ),
				$container->fetch('database' ),
				$container->fetch( 'options.user' )
			);
		} );
	}
}
