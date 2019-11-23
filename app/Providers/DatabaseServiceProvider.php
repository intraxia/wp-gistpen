<?php
namespace Intraxia\Gistpen\Providers;

use Intraxia\Jaxion\Contract\Core\Container;
use Intraxia\Jaxion\Contract\Core\ServiceProvider;
use Intraxia\Gistpen\Database\EntityManager;
use WP_Query;

/**
 * {@inheritDoc}
 */
class DatabaseServiceProvider implements ServiceProvider {
	/**
	 * {@inheritDoc}
	 *
	 * @param Container $container
	 */
	public function register( Container $container ) {
		$container->define( 'database', function () {
			return new EntityManager( 'wpgp' );
		} );
	}
}
