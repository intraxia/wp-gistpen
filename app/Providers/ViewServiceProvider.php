<?php
namespace Intraxia\Gistpen\Providers;

use Intraxia\Gistpen\View\Content;
use Intraxia\Gistpen\View\Editor;
use Intraxia\Gistpen\View\Settings;
use Intraxia\Jaxion\Contract\Core\Container;
use Intraxia\Jaxion\Contract\Core\ServiceProvider;

class ViewServiceProvider implements ServiceProvider {

	/**
	 * @inheritDoc
	 */
	public function register( Container $container ) {
		$container
			->define( 'view.content', function ( Container $container ) {
				return new Content(
					$container->fetch( 'database' ),
					$container->fetch( 'options.site' ),
					$container->fetch( 'templating' ),
					$container->fetch( 'url' )
				);
			} )
			->define( 'view.editor', function ( Container $container ) {
				return new Editor(
					$container->fetch( 'database' ),
					$container->fetch( 'options.user' ),
					$container->fetch( 'templating' ),
					$container->fetch( 'path' ),
					$container->fetch( 'url' )
				);
			} )
			->define( 'view.settings', function ( Container $container ) {
				return new Settings(
					$container->fetch( 'templating' ),
					$container->fetch( 'options.site' ),
					$container->fetch( 'basename' ),
					$container->fetch( 'url' )
				);
			} );
	}
}
