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
				return new Content( $container->fetch( 'facade.database' ) );
			} )
			->define( 'view.editor', function ( Container $container ) {
				return new Editor(
					$container->fetch( 'facade.database' ),
					$container->fetch( 'facade.adapter' ),
					$container->fetch( 'path' )
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
