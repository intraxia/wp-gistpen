<?php
namespace Intraxia\Gistpen\Providers;

use Github\Client;
use Intraxia\Gistpen\CLI\Command;
use Intraxia\Gistpen\Client\Gist;
use Intraxia\Gistpen\Facade\Adapter;
use Intraxia\Gistpen\Facade\Database;
use Intraxia\Gistpen\Migration;
use Intraxia\Gistpen\Register\Button;
use Intraxia\Gistpen\Register\Data;
use Intraxia\Gistpen\Save;
use Intraxia\Gistpen\Sync;
use Intraxia\Gistpen\View\Content;
use Intraxia\Gistpen\View\Editor;
use Intraxia\Gistpen\View\Settings;
use Intraxia\Jaxion\Contract\Core\Container;
use Intraxia\Jaxion\Contract\Core\ServiceProvider;
use WP_CLI;

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
			->define( 'account.gist', new Gist( $container->fetch( 'facade.adapter' ), new Client ) )
			->define( 'facade.database', new Database( $container->fetch( 'facade.adapter' ) ) )
			->define( 'view.editor', new Editor( $container->fetch( 'facade.database' ), $container->fetch( 'facade.adapter' ), $container->fetch( 'path' ) ) )
			->define( 'view.settings', new Settings( $container->fetch( 'account.gist' ), $container->fetch( 'basename' ), $container->fetch( 'path' ) ) )
			->define( 'view.content', new Content( $container->fetch( 'facade.database' ) ) )
			->define( 'migration', new Migration( $container->fetch( 'facade.database' ), $container->fetch( 'facade.adapter' ), $container->fetch( 'version' ) ) )
			->define( 'register.button', new Button( $container->fetch( 'url' ) ) )
			->define( 'sync', new Sync( $container->fetch( 'facade.database' ), $container->fetch( 'facade.adapter' ) ) )
			->define( 'save', new Save( $container->fetch( 'facade.database' ), $container->fetch( 'facade.adapter' ) ) )
			->define( 'cli.command', function () {
				return new Command;
			} );

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::add_command( 'gistpen', $container->fetch( 'cli.command' ) );
		}
	}
}
