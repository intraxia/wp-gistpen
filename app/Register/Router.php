<?php
namespace Intraxia\Gistpen\Register;

use Intraxia\Jaxion\Http\Filter;
use Intraxia\Jaxion\Http\Guard;
use Intraxia\Jaxion\Http\Router as CoreRouter;
use Intraxia\Gistpen\Http\Filter\BlobCreate as BlobCreateFilter;
use Intraxia\Gistpen\Http\Filter\BlobUpdate as BlobUpdateFilter;
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

		$router->group( array( 'prefix' => '/gistpen' ), function ( CoreRouter $router ) {
			$search = $this->container->get( \Intraxia\Gistpen\Http\SearchController::class );
			$user   = $this->container->get( \Intraxia\Gistpen\Http\UserController::class );
			$job    = $this->container->get( \Intraxia\Gistpen\Http\JobsController::class );
			$repo   = $this->container->get( \Intraxia\Gistpen\Http\RepoController::class );
			$blob   = $this->container->get( \Intraxia\Gistpen\Http\BlobController::class );
			$commit = $this->container->get( \Intraxia\Gistpen\Http\CommitController::class );
			$state  = $this->container->get( \Intraxia\Gistpen\Http\StateController::class );
			$site   = $this->container->get( \Intraxia\Gistpen\Http\SiteController::class );

			/**
			 * /repos endpoints
			 */
			$router->get( '/repos', array( $repo, 'index' ), array(
				'filter' => $this->container->get( RepoCollectionFilter::class ),
				'guard'  => new Guard(),
			) );
			$router->post( '/repos', array( $repo, 'create' ), array(
				'filter' => $this->container->get( RepoCreateFilter::class ),
				'guard'  => new Guard( array( 'rule' => 'can_edit_others_posts' ) ),
			) );

			/**
			 * /repos/{repo_id} endpoints
			 */
			$router->get( '/repos/(?P<id>\d+)', array( $repo, 'view' ), [
				'filter' => $this->container->get( RepoResourceFilter::class ),
				'guard'  => new Guard(),
			] );
			$router->put( '/repos/(?P<id>\d+)', array( $repo, 'update' ), array(
				'filter' => $this->container->get( RepoUpdateFilter::class ),
				'guard'  => new Guard( array( 'rule' => 'can_edit_others_posts' ) ),
			) );
			$router->patch( '/repos/(?P<id>\d+)', array( $repo, 'apply' ), array(
				'filter' => $this->container->get( RepoUpdateFilter::class ),
				'guard'  => new Guard( array( 'rule' => 'can_edit_others_posts' ) ),
			) );
			$router->delete( '/repos/(?P<id>\d+)', array( $repo, 'trash' ), array(
				'filter' => $this->container->get( RepoResourceFilter::class ),
				'guard'  => new Guard( array( 'rule' => 'can_edit_others_posts' ) ),
			) );

			/**
			 * /repos/{repo_id}/blobs endpoints
			 */
			$router->post( '/repos/(?P<repo_id>\d+)/blobs', [ $blob, 'create' ], [
				'filter' => $this->container->get( BlobCreateFilter::class ),
				'guard'  => new Guard( array( 'rule' => 'can_edit_others_posts' ) ),
			] );

			/**
			 * /repos/{repo_id}/blobs/{blob_id} endpoints
			 */
			$router->get( '/repos/(?P<repo_id>\d+)/blobs/(?P<blob_id>\d+)', [ $blob, 'view' ], [
				'guard' => new Guard(),
			] );
			$router->put( '/repos/(?P<repo_id>\d+)/blobs/(?P<blob_id>\d+)', [ $blob, 'update' ], [
				'filter' => $this->container->get( BlobUpdateFilter::class ),
				'guard'  => new Guard( array( 'rule' => 'can_edit_others_posts' ) ),
			] );
			$router->get( '/repos/(?P<repo_id>\d+)/blobs/(?P<blob_id>\d+)/raw', array( $blob, 'raw' ), [
				'guard' => new Guard(),
			] );

			/**
			 * /repos/{repo_id}/commits
			 */
			$router->get( '/repos/(?P<repo_id>\d+)/commits', array( $commit, 'index' ), [
				'guard' => new Guard(),
			] );

			/**
			 * /repos/{repo_id}/commits/{commit_id}/states
			 */
			$router->get( '/repos/(?P<repo_id>\d+)/commits/(?P<commit_id>\d+)/states', array( $state, 'index' ), [
				'guard' => new Guard(),
			] );

			/**
			 * /search endpoint
			 */
			$router->get(
				'/search/blobs',
				array( $search, 'blobs' ),
				[
					'filter' => $this->container->get( SearchFilter::class ),
					'guard'  => new Guard(),
				]
			);
			$router->get(
				'/search/repos',
				array( $search, 'repos' ),
				[
					'filter' => $this->container->get( SearchFilter::class ),
					'guard'  => new Guard(),
				]
			);

			/**
			 * /me endpoint
			 */
			$router->get( '/me', array( $user, 'view' ), array(
				'guard' => new Guard( array( 'rule' => 'user_logged_in' ) ),
			) );
			$router->patch( '/me', array( $user, 'update' ), array(
				'guard' => new Guard( array( 'rule' => 'user_logged_in' ) ),
			) );

			/**
			 * /site endpoint
			 */
			$router->get( '/site', array( $site, 'view' ), array(
				'guard' => new Guard( array( 'rule' => 'can_manage_options' ) ),
			) );
			$router->patch( '/site', array( $site, 'update' ), array(
				'filter' => $this->container->get( SitePatchFilter::class ),
				'guard'  => new Guard( array( 'rule' => 'can_manage_options' ) ),
			) );

			/**
			 * /jobs endpoint
			 */
			$router->get(
				'/jobs',
				array( $job, 'registered' ),
				array( 'guard' => new Guard( array( 'rule' => 'user_logged_in' ) ) )
			);
			$router->get(
				'/jobs/(?P<name>\w+)',
				array( $job, 'status' ),
				array( 'guard' => new Guard( array( 'rule' => 'user_logged_in' ) ) )
			);
			$router->post(
				'/jobs/(?P<name>\w+)',
				array( $job, 'dispatch' ),
				array( 'guard' => new Guard( array( 'rule' => 'user_logged_in' ) ) )
			);
			$router->post(
				'/jobs/(?P<name>\w+)/process',
				array( $job, 'process' ),
				array( 'guard' => new Guard( array( 'rule' => 'user_logged_in' ) ) )
			);

			/**
			 * /jobs/{name}/runs
			 */
			$router->get(
				'/jobs/(?P<name>\w+)/runs',
				array( $job, 'runs' ),
				array( 'guard' => new Guard( array( 'rule' => 'user_logged_in' ) ) )
			);
			$router->get(
				'/jobs/(?P<name>\w+)/runs/(?P<run_id>\w+)',
				array( $job, 'status' ),
				array( 'guard' => new Guard( array( 'rule' => 'user_logged_in' ) ) )
			);
			$router->get(
				'/jobs/(?P<name>\w+)/runs/(?P<run_id>\w+)/console',
				array( $job, 'console' ),
				array( 'guard' => new Guard( array( 'rule' => 'user_logged_in' ) ) )
			);
		} );
	}
}
