<?php
namespace Intraxia\Gistpen\Providers;

use Intraxia\Jaxion\Http\Filter;
use Intraxia\Jaxion\Http\Router;
use Intraxia\Jaxion\Http\RouterServiceProvider as ServiceProvider;

/**
 * Class RouterServiceProvider
 *
 * @package Intraxia\Gistpen
 * @subpackage Providers
 */
class RouterServiceProvider extends ServiceProvider {
	/**
	 * {@inheritDoc}
	 *
	 * @param Router $router
	 */
	protected function add_routes( Router $router ) {
		$router->set_vendor( 'intraxia' )->set_version( 1 );
		$controllers = array( // @todo this sucks, pass controller into router? how does router access the controllers?
			'search' => $this->container->fetch( 'controller.search' ),
		);

		$router->group( array( 'prefix' => '/gistpen' ), function ( Router $router ) use ( $controllers ) {
			$router->get(
				'/search',
				array( $controllers['search'], 'get' ),
				array(
					'filter' => new Filter( array(
						's'    => 'default',
						'type' => 'default:both|oneof:zip,type,both',
					) ),
				)
			);
		} );
	}
}
