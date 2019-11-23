<?php
namespace Intraxia\Gistpen\Providers;

use Intraxia\Gistpen\View\Button;
use Intraxia\Gistpen\View\Content;
use Intraxia\Gistpen\View\Edit;
use Intraxia\Gistpen\View\Settings;
use Intraxia\Jaxion\Contract\Core\Container;
use Intraxia\Jaxion\Contract\Core\ServiceProvider;

/**
 * {@inheritDoc}
 */
class ViewServiceProvider implements ServiceProvider {

	/**
	 * {@inheritDoc}
	 *
	 * @param Container $container
	 */
	public function register( Container $container ) {
		$container
			->define( 'view.content', function ( Container $container ) {
				return new Content(
					$container->fetch( 'params' ),
					$container->fetch( 'templating' ),
					$container->fetch( 'assets' )
				);
			} )
			->define( 'view.editor', function ( Container $container ) {
				return new Edit(
					$container->fetch( 'database' ),
					$container->fetch( 'params' ),
					$container->fetch( 'templating' ),
					$container->fetch( 'path' ),
					$container->fetch( 'url' )
				);
			} )
			->define( 'view.settings', function ( Container $container ) {
				return new Settings(
					$container->fetch( 'params' ),
					$container->fetch( 'templating' ),
					$container->fetch( 'basename' ),
					$container->fetch( 'url' )
				);
			} )
			->define( 'view.button', function ( Container $container ) {
				return new Button( $container->fetch( 'templating' ), $container->fetch( 'params' ), $container->fetch( 'url' ) );
			} );
	}
}
