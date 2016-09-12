<?php
namespace Intraxia\Gistpen\View;

use Intraxia\Gistpen\Client\Gist;
use Intraxia\Gistpen\Contract\Templating;
use Intraxia\Gistpen\Model\Language;
use Intraxia\Gistpen\Options\Site;
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
	 * @var Site
	 */
	private $site;
	/**
	 * @var
	 */
	private $url;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param Templating $template
	 * @param Site       $site
	 * @param string     $basename
	 * @param string     $url
	 *
	 * @since    0.5.0
	 */
	public function __construct( Templating $template, Site $site, $basename, $url ) {
		$this->template = $template;
		$this->site     = $site;
		$this->basename = $basename;
		$this->url      = $url;
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
		echo $this->template->render( 'settings/index', $this->get_initial_state() );
	}

	/**
	 * Generates the initial state for the page.
	 *
	 * @return array
	 */
	public function get_initial_state() {
		return array(
			'route' => 'bootstrap',
			'prism' => $this->site->get( 'prism' ),
			'gist'  => $this->site->get( 'gist' ),
			'const' => array(
				'languages'  => Language::$supported,
				'root'       => esc_url_raw( rest_url() . 'intraxia/v1/gistpen/' ),
				'nonce'      => wp_create_nonce( 'wp_rest' ),
				'url'        => $this->url,
				'ace_themes' => Editor::$ace_themes,
				'ace_widths' => array( 1, 2, 4, 8 ),
				'statuses'   => get_post_statuses(),
				'themes'     => array(
					'default'                         => __( 'Default', 'wp-gistpen' ),
					'dark'                            => __( 'Dark', 'wp-gistpen' ),
					'funky'                           => __( 'Funky', 'wp-gistpen' ),
					'okaidia'                         => __( 'Okaidia', 'wp-gistpen' ),
					'tomorrow'                        => __( 'Tomorrow', 'wp-gistpen' ),
					'twilight'                        => __( 'Twilight', 'wp-gistpen' ),
					'coy'                             => __( 'Coy', 'wp-gistpen' ),
					'cb'                              => __( 'CB', 'wp-gistpen' ),
					'ghcolors'                        => __( 'GHColors', 'wp-gistpen' ),
					'pojoaque'                        => __( 'Projoaque', 'wp-gistpen' ),
					'xonokai'                         => __( 'Xonokai', 'wp-gistpen' ),
					'base16-ateliersulphurpool.light' => __( 'Ateliersulphurpool-Light', 'wp-gistpen' ),
					'hopscotch'                       => __( 'Hopscotch', 'wp-gistpen' ),
					'atom-dark'                       => __( 'Atom Dark', 'wp-gistpen' ),
				),
			),
		);
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
				'hook'   => 'admin_menu',
				'method' => 'add_plugin_admin_menu',
			),
			array(
				'hook'   => 'admin_init',
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
				'hook'   => 'plugin_action_links_' . $this->basename,
				'method' => 'add_action_links',
			)
		);
	}
}
