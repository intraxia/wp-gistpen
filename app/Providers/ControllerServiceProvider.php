<?php
namespace Intraxia\Gistpen\Providers;

use Intraxia\Gistpen\Http\CommitController;
use Intraxia\Gistpen\Http\JobsController;
use Intraxia\Gistpen\Http\SearchController;
use Intraxia\Gistpen\Http\SiteController;
use Intraxia\Gistpen\Http\StateController;
use Intraxia\Gistpen\Http\UserController;
use Intraxia\Gistpen\Http\RepoController;
use Intraxia\Gistpen\Http\BlobController;
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
		$container->share( array( 'controller.search' => 'Intraxia\Gistpen\Http\SearchController' ), function ( Container $container) {
			return new SearchController( $container->fetch( 'database' ) );
		} );

		$container->share( array( 'controller.user' => 'Intraxia\Gistpen\Http\UserController' ), function ( Container $container) {
			return new UserController( $container->fetch( 'options.user' ) );
		} );

		$container->share( array( 'controller.site' => 'Intraxia\Gistpen\Http\SiteController' ), function ( Container $container) {
			return new SiteController( $container->fetch( 'options.site' ) );
		} );

		$container->share( array( 'controller.job' => 'Intraxia\Gistpen\Http\JobsController' ), function ( Container $container) {
			return new JobsController( $container->fetch( 'jobs' ), $container->fetch( 'database' ) );
		} );

		$container->share( array( 'controller.repo' => 'Intraxia\Gistpen\Http\RepoController' ), function ( Container $container) {
			return new RepoController( $container->fetch( 'database' ) );
		} );
		$container->share( array( 'controller.blob' => 'Intraxia\Gistpen\Http\BlobController' ), function ( Container $container) {
			return new BlobController( $container->fetch( 'database' ) );
		} );
		$container->share( array( 'controller.commit' => 'Intraxia\Gistpen\Http\BlobController' ), function( Container $container) {
			return new CommitController( $container->fetch( 'database' ) );
		} );
		$container->share( array( 'controller.state' => 'Intraxia\Gistpen\Http\BlobController' ), function( Container $container) {
			return new StateController( $container->fetch( 'database' ) );
		} );

	}
}
