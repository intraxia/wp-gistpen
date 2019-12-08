<?php
namespace Intraxia\Gistpen;

use Intraxia\Gistpen\Listener\Migration;
use Intraxia\Jaxion\Core\Application;
use Intraxia\Jaxion\Contract\Core\HasActions;
use Intraxia\Jaxion\Core\Config;

/**
 * The lifecycle class.
 *
 * Manages plugin activation & deactivation.
 *
 * @package    Intraxia\Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      2.0.0
 */
class Lifecycle implements HasActions {

	/**
	 * Create a new Lifecycle class.
	 *
	 * @param Config    $config    App configuration.
	 * @param Migration $migration Database migration.
	 */
	public function __construct( Config $config, Migration $migration ) {
		$this->config    = $config;
		$this->migration = $migration;
	}

	/**
	 * Register the plugin lifecycle methods.
	 */
	public function init() {
		register_activation_hook( $this->config->file, [ $this, 'activate' ] );
		register_deactivation_hook( $this->config->file, [ $this, 'deactivate' ] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function activate() {
		if ( ! get_option( '_wpgp_activated' ) ) {
			$this->migration->run();
		}

		update_option( '_wpgp_activated', 'done' );
		flush_rewrite_rules( true );
	}

	/**
	 * {@inheritdoc}
	 */
	public function deactivate() {
		flush_rewrite_rules( true );
	}

	/**
	 * {@inheritDoc}
	 */
	public function action_hooks() {
		return [
			[
				'hook'   => 'plugins_loaded',
				'method' => 'init',
			],
		];
	}
}
