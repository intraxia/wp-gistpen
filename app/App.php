<?php
namespace WP_Gistpen;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class App extends \Intraxia\Jaxion\Core\Application {

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 * @since    0.5.0
	 */
	public function __construct( $file ) {
		parent::__construct( $file );

		$plugin_i18n = new Register\I18n();
		$plugin_i18n->set_domain( \WP_Gistpen::$plugin_name );
		add_action( 'plugins_loaded', array( $plugin_i18n, 'load_plugin_textdomain' ) );

		/**
		 * Register Api Endpoints
		 */
		$this['Api\Ajax'] = function() {
			return new Api\Ajax();
		};

		/**
		 * Register Controllers
		 */
		$this['Controller\Save'] = function() {
			return new Controller\Save();
		};
		$this['Controller\Sync'] = function() {
			return new Controller\Sync();
		};

		/**
		 * Register Migration Script
		 */
		$this['Migration'] = function() {
			return new Migration();
		};

		/**
		 * Register Script/Style Assets
		 */
		$this['Register\Assets'] = function($app) {
			$assets = new Register\Assets( $app['url'] );

			if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
				$assets->setDebug( true );
			}

			return $assets;
		};

		/**
		 * Register TinyMCE Button Assets
		 */
		$this['Register\Button'] = function($app) {
			return new Register\Button( $app['url'] );
		};

		/**
		 * Register Custom Post Types/Taxonomies
		 */
		$this['Register\Data'] = function() {
			return new Register\Data();
		};
		// @todo push into framework
		add_shortcode( 'gistpen', array( $this['Register\Data'], 'add_shortcode' ) );

		/**
		 * Register Views
		 */
		$this['View\Content'] = function() {
			return new View\Content();
		};
		$this['View\Editor'] = function() {
			return new View\Editor();
		};
		$this['View\Settings'] = function($app) {
			return new View\Settings( $app['basename'] );
		};

		/**
		 * Register Command
		 */
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			// @todo push into framework
			\WP_CLI::add_command( 'wpgp', 'WP_Gistpen\CLI\Command' );
		}
	}
}
