<?php
namespace Intraxia\Gistpen\Providers;

use Intraxia\Gistpen\Http\SearchController;
use Intraxia\Gistpen\Http\ZipController;
use Intraxia\Jaxion\Contract\Core\Container;
use Intraxia\Jaxion\Contract\Core\ServiceProvider;

/**
 * Class ControllerServiceProvider
 *
 * @package Intraxia\Gistpen
 * @subpackage Providers
 */
class ControllerServiceProvider implements ServiceProvider {
	/**
	 * {@inheritdoc}
	 *
	 * @param Container $container
	 */
	public function register( Container $container ) {
		$container->share( array( 'controller.search' => 'Intraxia\Gistpen\Http\SearchController' ), function ( $app ) {
			return new SearchController( $app->fetch( 'facade.database' ), $app->fetch( 'facade.adapter' ) );
		} );

		$container->share( array( 'controller.zip' => 'Intraxia\Http\Http\ZipController' ), function ( $app ) {
			return new ZipController( $app->fetch( 'facade.database' ), $app->fetch( 'facade.adapter' ) );
		} );
	}
}
