<?php
namespace Intraxia\Gistpen\Providers;

use Intraxia\Gistpen\Account\Gist;
use Intraxia\Gistpen\Facade\Adapter;
use Intraxia\Gistpen\Facade\Database;
use Intraxia\Gistpen\Migration;
use Intraxia\Gistpen\Register\Button;
use Intraxia\Gistpen\Register\Data;
use Intraxia\Gistpen\Sync;
use Intraxia\Gistpen\View\Content;
use Intraxia\Gistpen\View\Editor;
use Intraxia\Gistpen\View\Settings;
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
			->define( 'adapter', new Adapter )
			->define( 'account.gist', new Gist( $container->fetch( 'adapter' ) ) )
			->define( 'database', new Database( $container->fetch( 'adapter' ) ) )
			->define( 'view.editor', new Editor( $container->fetch( 'database' ), $container->fetch( 'adapter' ), $container->fetch( 'path' ) ) )
			->define( 'view.settings', new Settings( $container->fetch( 'account.gist' ), $container->fetch( 'basename' ), $container->fetch( 'path' ) ) )
			->define( 'view.content', new Content( $container->fetch( 'database' ) ) )
			->define( 'migration', new Migration( $container->fetch( 'database' ), $container->fetch( 'adapter' ), $container->fetch( 'version' ) ) )
			->define( 'register.button', new Button( $container->fetch( 'url' ) ) )
			->define( 'sync', new Sync( $container->fetch( 'database' ), $container->fetch( 'adapter' ) ) );
	}
}
