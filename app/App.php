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
	 * @inheritdoc
	 */
	public function __construct( $file ) {
		parent::__construct( $file );

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
		$this['View\Editor'] = function($app) {
			return new View\Editor( $app['path'] );
		};
		$this['View\Settings'] = function($app) {
			return new View\Settings( $app['basename'], $app['path'] );
		};

		/**
		 * Register Command
		 */
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			// @todo push into framework
			$this['CLI\Command'] = function($app) {
				return new CLI\Command( $app['path'] );
			};
			\WP_CLI::add_command( 'wpgp', $this['CLI\Command'] );
		}
	}

	/**
	 * @inheritdoc
	 */
	public function activate() {
		if ( ! get_option( '_wpgp_activated' ) ) {
			update_option( 'wp_gistpen_version', \WP_Gistpen::$version );
		}

		update_option( '_wpgp_activated', 'done' );
		flush_rewrite_rules( true );
	}

	/**
	 * @inheritdoc
	 */
	public function deactivate() {
		flush_rewrite_rules( true );
	}

}
