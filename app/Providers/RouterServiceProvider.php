<?php
namespace Intraxia\Gistpen\Providers;

use Intraxia\Jaxion\Http\Filter;
use Intraxia\Jaxion\Http\Guard;
use Intraxia\Jaxion\Http\Router;
use Intraxia\Jaxion\Http\ServiceProvider;
use Intraxia\Gistpen\Http\Filter\RepoCreate as RepoCreateFilter;
use Intraxia\Gistpen\Http\Filter\RepoRequest as RepoRequestFilter;

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
			'user'   => $this->container->fetch( 'controller.user' ),
			'job'    => $this->container->fetch( 'controller.job' ),
			'repo'   => $this->container->fetch( 'controller.repo' ),
			'blob'   => $this->container->fetch( 'controller.blob' ),
			'commit' => $this->container->fetch( 'controller.commit' ),
			'state'  => $this->container->fetch( 'controller.state' ),
			'site'   => $this->container->fetch( 'controller.site' ),
		);

		$router->group( array( 'prefix' => '/gistpen' ), function ( Router $router ) use ( $controllers ) {
			/**
			 * /repos endpoints
			 */
			$router->get( '/repos', array( $controllers['repo'], 'index' ), array(
				'filter' => new RepoRequestFilter,
			) );
			$router->post( '/repos', array( $controllers['repo'], 'create' ), array(
				'filter' => new RepoCreateFilter,
				'guard'  => new Guard( array( 'rule' => 'can_edit_others_posts' ) ),
			) );

			/**
			 * /repos/{repo_id} endpoints
			 */
			$router->get( '/repos/(?P<id>\d+)', array( $controllers['repo'], 'view' ) );
			$router->put( '/repos/(?P<id>\d+)', array( $controllers['repo'], 'update' ), array(
				'filter' => new RepoCreateFilter,
				'guard'  => new Guard( array( 'rule' => 'can_edit_others_posts' ) ),
			) );
			$router->patch( '/repos/(?P<id>\d+)', array( $controllers['repo'], 'apply' ), array(
				'filter' => new RepoCreateFilter,
				'guard'  => new Guard( array( 'rule' => 'can_edit_others_posts' ) ),
			) );
			$router->delete( '/repos/(?P<id>\d+)', array( $controllers['repo'], 'trash' ), array(
				'filter' => new RepoCreateFilter,
				'guard'  => new Guard( array( 'rule' => 'can_edit_others_posts' ) ),
			) );

			/**
			 * /repos/{repo_id}/blobs/{blob_id} endpoints
			 */
			$router->get( '/repos/(?P<repo_id>\d+)/blobs/(?P<blob_id>\d+)/raw', array( $controllers['blob'], 'raw' ) );

			/**
			 * /repos/{repo_id}/commits
			 */
			$router->get( '/repos/(?P<repo_id>\d+)/commits', array( $controllers['commit'], 'index' ) );

			/**
			 * /repos/{repo_id}/commits/{commit_id}/states
			 */
			$router->get( '/repos/(?P<repo_id>\d+)/commits/(?P<commit_id>\d+)/states', array( $controllers['state'], 'index' ) );

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
			 * /site endpoint
			 */
			$router->get( '/site', array( $controllers['site'], 'view' ), array(
				'guard' => new Guard( array( 'rule' => 'user_logged_in' ) ),
			) );
			$router->patch( '/site', array( $controllers['site'], 'update' ), array(
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
				'/jobs/(?P<name>\w+)/process',
				array( $controllers['job'], 'process' ),
				array( 'guard' => new Guard( array( 'rule' => 'user_logged_in' ) ) )
			);

			/**
			 * /jobs/{name}/runs
			 */
			$router->get(
				'/jobs/(?P<name>\w+)/runs',
				array( $controllers['job'], 'runs' ),
				array( 'guard' => new Guard( array( 'rule' => 'user_logged_in' ) ) )
			);
			$router->get(
				'/jobs/(?P<name>\w+)/runs/(?P<run_id>\w+)',
				array( $controllers['job'], 'status' ),
				array( 'guard' => new Guard( array( 'rule' => 'user_logged_in' ) ) )
			);
			$router->get(
				'/jobs/(?P<name>\w+)/runs/(?P<run_id>\w+)/console',
				array( $controllers['job'], 'console' ),
				array( 'guard' => new Guard( array( 'rule' => 'user_logged_in' ) ) )
			);
		} );
	}
}
