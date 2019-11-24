<?php
namespace Intraxia\Gistpen\Providers;

use Intraxia\Gistpen\Jobs\ExportJob;
use Intraxia\Gistpen\Jobs\ImportJob;
use Intraxia\Gistpen\Jobs\Manager as Jobs;
use Intraxia\Jaxion\Contract\Core\Container;
use Intraxia\Jaxion\Contract\Core\ServiceProvider;

/**
 * {@inheritDoc}
 */
class JobsServiceProvider implements ServiceProvider {

	/**
	 * Register the provider's services on the container.
	 *
	 * This method is passed the container to register on, giving the service provider
	 * an opportunity to register its services on the container in an encapsulated way.
	 *
	 * @param Container $container
	 */
	public function register( Container $container ) {
		$container->define( 'job.export', function ( Container $container ) {
			return new ExportJob( $container->fetch( 'database' ), $container->fetch( 'client.gist' ) );
		} );

		$container->define( 'job.import', function ( Container $container ) {
			return new ImportJob( $container->fetch( 'database' ), $container->fetch( 'client.gist' ) );
		} );

		$container->define( 'jobs', function ( Container $container ) {
			$jobs = new Jobs();

			$jobs->add_job( 'export', $container->fetch( 'job.export' ) );
			$jobs->add_job( 'import', $container->fetch( 'job.import' ) );

			return $jobs;
		} );
	}
}
