<?php
namespace Intraxia\Gistpen\View;

use Intraxia\Gistpen\Client\Gist;
use Intraxia\Gistpen\Contract\Templating;
use Intraxia\Jaxion\Contract\Core\HasActions;
use Intraxia\Jaxion\Contract\Core\HasFilters;

/**
 * This class registers all of the settings page views
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Settings implements HasActions, HasFilters {
	/**
	 * Templating service.
	 *
	 * @var Templating
	 */
	private $template;

	/**
	 * Plugin basename
	 *
	 * @var string
	 */
	protected $basename;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param Templating $template
	 * @param string     $basename
	 *
	 * @since    0.5.0
	 */
	public function __construct( Templating $template, $basename ) {
		$this->template = $template;
		$this->basename = $basename;
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    0.1.0
	 */
	public function add_plugin_admin_menu() {
		add_options_page(
			__( 'WP-Gistpen Settings', 'wp-gistpen' ),
			__( 'Gistpens', 'wp-gistpen' ),
			'edit_posts',
			'wp-gistpen',
			array( $this, 'display_plugin_admin_page' )
		);
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    0.1.0
	 */
	public function display_plugin_admin_page() {
		echo $this->template->render( 'settings/index', array() );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    0.1.0
	 */
	public function add_action_links( $links ) {
		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . 'wp-gistpen' ) . '">' . __( 'Settings', 'wp-gistpen' ) . '</a>'
			),
			$links
		);
	}

	/**
	 * Register the settings page (obviously)
	 *
	 * @since 0.3.0
	 */
	public function register_setting() {
		register_setting( 'wp-gistpen', 'wp-gistpen' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return array[]
	 */
	public function action_hooks() {
		return array(
			array(
				'hook' => 'admin_menu',
				'method' => 'add_plugin_admin_menu',
			),
			array(
				'hook' => 'admin_init',
				'method' => 'register_setting',
			),
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return array[]
	 */
	public function filter_hooks() {
		return array(
			array(
				'hook' => 'plugin_action_links_' . $this->basename,
				'method' => 'add_action_links',
			)
		);
	}
}
