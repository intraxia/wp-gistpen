<?php
namespace Intraxia\Gistpen\Register;

use Intraxia\Jaxion\Http\Filter;
use Intraxia\Jaxion\Http\Guard;
use Intraxia\Jaxion\Http\Router as CoreRouter;
use Intraxia\Gistpen\Http\Filter\BlobCreate as BlobCreateFilter;
use Intraxia\Gistpen\Http\Filter\RepoCollection as RepoCollectionFilter;
use Intraxia\Gistpen\Http\Filter\RepoCreate as RepoCreateFilter;
use Intraxia\Gistpen\Http\Filter\RepoUpdate as RepoUpdateFilter;
use Intraxia\Gistpen\Http\Filter\RepoResource as RepoResourceFilter;
use Intraxia\Gistpen\Http\Filter\Search as SearchFilter;
use Intraxia\Gistpen\Http\Filter\SitePatch as SitePatchFilter;
use Psr\Container\ContainerInterface as Container;

/**
 * Class Router
 *
 * @package Intraxia\Gistpen
 * @subpackage Register
 */
class Router {

	/**
	 * Container service.
	 *
	 * @var Container
	 */
	protected $container;

	/**
	 * Constructor.
	 *
	 * @param Container $container
	 */
	public function __construct( Container $container ) {
		$this->container = $container;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param CoreRouter $router
	 */
	public function add_routes( CoreRouter $router ) {
		$router->set_vendor( 'intraxia' )->set_version( 1 );
		$controllers = array(
			// @todo this sucks, pass controller into router? how does router access the controllers?
			'search' => $this->container->get( \Intraxia\Gistpen\Http\SearchController::class ),
			'user'   => $this->container->get( \Intraxia\Gistpen\Http\UserController::class ),
			'job'    => $this->container->get( \Intraxia\Gistpen\Http\JobsController::class ),
			'repo'   => $this->container->get( \Intraxia\Gistpen\Http\RepoController::class ),
			'blob'   => $this->container->get( \Intraxia\Gistpen\Http\BlobController::class ),
			'commit' => $this->container->get( \Intraxia\Gistpen\Http\CommitController::class ),
			'state'  => $this->container->get( \Intraxia\Gistpen\Http\StateController::class ),
			'site'   => $this->container->get( \Intraxia\Gistpen\Http\SiteController::class ),
		);

		$router->group( array( 'prefix' => '/gistpen' ), function ( CoreRouter $router ) use ( $controllers ) {
			/**
			 * /repos endpoints
			 */
			$router->get( '/repos', array( $controllers['repo'], 'index' ), array(
				'filter' => $this->container->get( RepoCollectionFilter::class ),
			) );
			$router->post( '/repos', array( $controllers['repo'], 'create' ), array(
				'filter' => $this->container->get( RepoCreateFilter::class ),
				'guard'  => new Guard( array( 'rule' => 'can_edit_others_posts' ) ),
			) );

			/**
			 * /repos/{repo_id} endpoints
			 */
			$router->get( '/repos/(?P<id>\d+)', array( $controllers['repo'], 'view' ), [
				'filter' => $this->container->get( RepoResourceFilter::class ),
			] );
			$router->put( '/repos/(?P<id>\d+)', array( $controllers['repo'], 'update' ), array(
				'filter' => $this->container->get( RepoUpdateFilter::class ),
				'guard'  => new Guard( array( 'rule' => 'can_edit_others_posts' ) ),
			) );
			$router->patch( '/repos/(?P<id>\d+)', array( $controllers['repo'], 'apply' ), array(
				'filter' => $this->container->get( RepoUpdateFilter::class ),
				'guard'  => new Guard( array( 'rule' => 'can_edit_others_posts' ) ),
			) );
			$router->delete( '/repos/(?P<id>\d+)', array( $controllers['repo'], 'trash' ), array(
				'filter' => $this->container->get( RepoResourceFilter::class ),
				'guard'  => new Guard( array( 'rule' => 'can_edit_others_posts' ) ),
			) );

			/**
			 * /repos/{repo_id}/blobs endpoints
			 */
			$router->post( '/repos/(?P<repo_id>\d+)/blobs', [ $controllers['blob'], 'create' ], [
				'filter' => $this->container->get( BlobCreateFilter::class ),
				'guard'  => new Guard( array( 'rule' => 'can_edit_others_posts' ) ),
			] );

			/**
			 * /repos/{repo_id}/blobs/{blob_id} endpoints
			 */
			$router->get( '/repos/(?P<repo_id>\d+)/blobs/(?P<blob_id>\d+)', [ $controllers['blob'], 'view' ] );
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
				'/search/blobs',
				array( $controllers['search'], 'blobs' ),
				[ 'filter' => $this->container->get( SearchFilter::class ) ]
			);
			$router->get(
				'/search/repos',
				array( $controllers['search'], 'repos' ),
				[ 'filter' => $this->container->get( SearchFilter::class ) ]
			);

			/**
			 * /me endpoint
			 */
			$router->get( '/me', array( $controllers['user'], 'view' ), array(
				'guard' => new Guard( array( 'rule' => 'user_logged_in' ) ),
			) );
			$router->patch( '/me', array( $controllers['user'], 'update' ), array(
				'guard' => new Guard( array( 'rule' => 'user_logged_in' ) ),
			) );

			/**
			 * /site endpoint
			 */
			$router->get( '/site', array( $controllers['site'], 'view' ), array(
				'guard' => new Guard( array( 'rule' => 'can_manage_options' ) ),
			) );
			$router->patch( '/site', array( $controllers['site'], 'update' ), array(
				'filter' => $this->container->get( SitePatchFilter::class ),
				'guard'  => new Guard( array( 'rule' => 'can_manage_options' ) ),
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
