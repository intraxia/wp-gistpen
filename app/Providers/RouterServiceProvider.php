<?php
namespace Intraxia\Gistpen\Providers;

use Intraxia\Gistpen\Http\ZipFilter;
use Intraxia\Jaxion\Http\Filter;
use Intraxia\Jaxion\Http\Guard;
use Intraxia\Jaxion\Http\Router;
use Intraxia\Jaxion\Http\ServiceProvider as ServiceProvider;

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
			'zip'    => $this->container->fetch( 'controller.zip' ),
			'user'   => $this->container->fetch( 'controller.user' ),
			'job'    => $this->container->fetch( 'controller.job' ),
		);

		$router->group( array( 'prefix' => '/gistpen' ), function ( Router $router ) use ( $controllers ) {
			/**
			 * /zip endpoint
			 */
			$router->get( '/zip/(?P<id>\d+)', array( $controllers['zip'], 'view' ), array(
				'filter' => new Filter( array( 'id' => 'required|integer' ) ),
			) );
			$router->post( '/zip', array( $controllers['zip'], 'create' ), array(
				'filter' => new ZipFilter,
				'guard'  => new Guard( array( 'rule' => 'can_edit_others_posts' ) ),
			) );
			$router->put( '/zip/(?P<id>\d+)', array( $controllers['zip'], 'update' ), array(
				'filter' => new ZipFilter,
				'guard'  => new Guard( array( 'rule' => 'can_edit_others_posts' ) ),
			) );

			/**
			 * /search endpoint
			 */
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

			/**
			 * /me endpoint
			 */
			$router->get( '/me', array( $controllers['user'], 'view' ), array(
				'guard' => new Guard( array( 'rule' => 'user_logged_in' ) ),
			) );
			$router->patch( '/me', array( $controllers['user'], 'update' ), array(
				'guard'  => new Guard( array( 'rule' => 'user_logged_in' ) ),
			) );

			/**
			 * /jobs endpoint
			 */
			$router->get(
				'/jobs',
				array( $controllers['job'], 'registered' ),
				array( 'guard' => new Guard( array( 'rule' => 'user_logged_in' ) ) )
			);
			$router->get(
				'/jobs/(?P<name>\w+)',
				array( $controllers['job'], 'status' ),
				array( 'guard' => new Guard( array( 'rule' => 'user_logged_in' ) ) )
			);
			$router->post(
				'/jobs/(?P<name>\w+)',
				array( $controllers['job'], 'dispatch' ),
				array( 'guard' => new Guard( array( 'rule' => 'user_logged_in' ) ) )
			);
			$router->post(
				'/jobs/(?P<name>\w+)/next',
				array( $controllers['job'], 'next' ),
				array( 'guard' => new Guard( array( 'rule' => 'user_logged_in' ) ) )
			);
			$router->get(
				'/jobs/(?P<name>\w+)/(?P<timestamp>\w+)',
				array( $controllers['job'], 'status' ),
				array( 'guard' => new Guard( array( 'rule' => 'user_logged_in' ) ) )
			);
			$router->get(
				'/jobs/(?P<name>\w+)/(?P<timestamp>\w+)/console',
				array( $controllers['job'], 'console' ),
				array( 'guard' => new Guard( array( 'rule' => 'user_logged_in' ) ) )
			);
		} );
	}
}
