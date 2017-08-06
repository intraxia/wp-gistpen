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
	const VERSION = '1.0.0';

	/**
	 * ServiceProviders to register with the Application
	 *
	 * @var string[]
	 */
	protected $providers = array(
		'Intraxia\Gistpen\Providers\ConfigServiceProvider',
		'Intraxia\Gistpen\Providers\ClientServiceProvider',
		'Intraxia\Gistpen\Providers\ViewServiceProvider',
		'Intraxia\Gistpen\Providers\TemplatingServiceProvider',
		'Intraxia\Gistpen\Providers\OptionsServiceProvider',
		'Intraxia\Gistpen\Providers\AssetsServiceProvider',
		'Intraxia\Gistpen\Providers\DatabaseServiceProvider',
		'Intraxia\Gistpen\Providers\JobsServiceProvider',
		'Intraxia\Gistpen\Providers\ControllerServiceProvider',
		'Intraxia\Gistpen\Providers\CoreServiceProvider',
		'Intraxia\Gistpen\Providers\EmbedServiceProvider',
		'Intraxia\Gistpen\Providers\RouterServiceProvider',
		'Intraxia\Gistpen\Providers\ParamsServiceProvider',
		'Intraxia\Gistpen\Providers\ListenerServiceProvider',
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
