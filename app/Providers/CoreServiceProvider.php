<?php
namespace Intraxia\Gistpen\Providers;

use Intraxia\Gistpen\Facade\Adapter;
use Intraxia\Gistpen\Facade\Database;
use Intraxia\Gistpen\Migration;
use Intraxia\Gistpen\Register\Data;
use Intraxia\Gistpen\Save;
use Intraxia\Jaxion\Contract\Core\Container;
use Intraxia\Jaxion\Contract\Core\ServiceProvider;

/**
 * Class CoreServiceProvider
 *
 * @package Intraxia\Gistpen
 * @subpackage Providers
 */
class CoreServiceProvider implements ServiceProvider {
	/**
	 * {@inheritDoc}
	 *
	 * @param Container $container
	 */
	public function register( Container $container ) {
		$container
			->define( 'register.data', new Data )
			->define( 'facade.adapter', new Adapter )
			->define( 'facade.database', new Database( $container->fetch( 'facade.adapter' ) ) )
			->define(
				'save',
				new Save(
					$container->fetch( 'facade.database' ),
					$container->fetch( 'facade.adapter' )
				)
			);
	}
}
