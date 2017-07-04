<?php
namespace Intraxia\Gistpen\Providers;

use Intraxia\Gistpen\View\Button;
use Intraxia\Gistpen\View\Content;
use Intraxia\Gistpen\View\Edit;
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
					$container->fetch( 'assets' ),
					$container->fetch( 'url' )
				);
			} )
			->define( 'view.editor', function ( Container $container ) {
				return new Edit(
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
					$container->fetch( 'database' ),
					$container->fetch( 'basename' ),
					$container->fetch( 'url' )
				);
			} )
			->define( 'view.button', function ( Container $container ) {
				return new Button( $container->fetch('templating'), $container->fetch( 'params' ), $container->fetch( 'url' ) );
			} );
	}
}
