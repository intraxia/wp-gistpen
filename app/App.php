<?php
namespace Intraxia\Gistpen;

use Intraxia\Gistpen\Database\EntityManager;
use Intraxia\Jaxion\Core\Application;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @package    Intraxia\Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class App extends Application {
	/**
	 * Plugin version constant.
	 */
	const VERSION = '1.1.6';

	/**
	 * ServiceProviders to register with the Application
	 *
	 * @var string[]
	 */
	protected $providers = array(
		\Intraxia\Gistpen\Providers\ClientServiceProvider::class,
		\Intraxia\Gistpen\Providers\ViewServiceProvider::class,
		\Intraxia\Gistpen\Providers\TranslationsServiceProvider::class,
		\Intraxia\Gistpen\Providers\TemplatingServiceProvider::class,
		\Intraxia\Gistpen\Providers\OptionsServiceProvider::class,
		\Intraxia\Gistpen\Providers\AssetsServiceProvider::class,
		\Intraxia\Gistpen\Providers\DatabaseServiceProvider::class,
		\Intraxia\Gistpen\Providers\JobsServiceProvider::class,
		\Intraxia\Gistpen\Providers\ControllerServiceProvider::class,
		\Intraxia\Gistpen\Providers\CoreServiceProvider::class,
		\Intraxia\Gistpen\Providers\EmbedServiceProvider::class,
		\Intraxia\Gistpen\Providers\RouterServiceProvider::class,
		\Intraxia\Gistpen\Providers\ParamsServiceProvider::class,
		\Intraxia\Gistpen\Providers\ListenerServiceProvider::class,
	);

	/**
	 * {@inheritdoc}
	 */
	public function activate() {
		if ( ! get_option( '_wpgp_activated' ) ) {
			$this->fetch( 'listener.migration' )->run();
		}

		update_option( '_wpgp_activated', 'done' );
		flush_rewrite_rules( true );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @codeCoverageIgnore
	 */
	public function deactivate() {
		flush_rewrite_rules( true );
	}
}
