<?php
namespace Intraxia\Gistpen\Register;

use Intraxia\Gistpen\Model\Repo;
use Intraxia\Gistpen\Params\Repository as Params;
use Intraxia\Gistpen\View\Edit;
use Intraxia\Gistpen\View\Settings;
use Intraxia\Jaxion\Assets\Register;
use Psr\Container\ContainerInterface as Container;

/**
 * Class Register\Assets.
 *
 * @package    Intraxia\Gistpen
 * @subpackage Register
 */
class Assets {

	/**
	 * Common dependencies
	 *
	 * @var string[]
	 */
	protected static $deps = array(
		'react',
		'react-dom',
		'wp-blocks',
		'wp-i18n',
		'wp-components',
		'wp-element',
		'wp-compose',
	);

	/**
	 * Container service.
	 *
	 * @var Container
	 */
	protected $container;

	/**
	 * Constructor.
	 *
	 * @param Container $container
	 */
	public function __construct( Container $container ) {
		$this->container = $container;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param Register $assets
	 */
	public function add_assets( Register $assets ) {
		$assets->set_debug( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG );

		$slug = $this->container->get( 'slug' );

		/**
		 * Edit Assets
		 */
		$assets->register_script( array(
			'type'      => 'admin',
			'deps'      => static::$deps,
			'condition' => function () {
				$cond = get_current_screen()->id === 'gistpen';

				if ( $cond ) {
					wp_dequeue_script( 'autosave' );
				}

				return $cond;
			},
			'handle'    => $slug . '-editor-script',
			'src'       => 'assets/js/editor',
			'footer'    => false,
			'localize'  => function () {
				/**
				 * Params service.
				 *
				 * @var Params
				 */
				$params = $this->container->get( Params::class );

				return array(
					'name' => '__GISTPEN_EDITOR__',
					'data' => $params->state( 'edit' ),
				);
			},
		) );

		/**
		 * Settings Page Assets
		 */
		$assets->register_script( array(
			'type'      => 'admin',
			'deps'      => static::$deps,
			'condition' => function () {
				return 'settings_page_wp-gistpen' === get_current_screen()->id;
			},
			'handle'    => $slug . '-settings-script',
			'src'       => 'assets/js/settings',
			'footer'    => true,
			'localize'  => function () {
				/**
				 * Params service.
				 *
				 * @var Params
				 */
				$params = $this->container->get( Params::class );

				return array(
					'name' => '__GISTPEN_SETTINGS__',
					'data' => $params->state( 'settings' ),
				);
			},
		) );

		/**
		 * Content Assets
		 */
		$assets->register_script( array(
			'type'      => 'web',
			'condition' => function () {
				if ( is_home() || is_archive() ) {
					return true;
				}

				if ( Repo::get_post_type() === get_post_type() ) {
					return true;
				}

				$post = get_post();

				if ( ! $post ) {
					return false;
				}

				return has_shortcode( $post->post_content, 'gistpen' );
			},
			'handle'    => $slug . '-content-script',
			'src'       => 'assets/js/content',
			'footer'    => true,
			'localize'  => function() {
				/**
				 * Params service.
				 *
				 * @var Params
				 */
				$params = $this->container->get( Params::class );

				return array(
					'name' => '__GISTPEN_CONTENT__',
					'data' => $params->state( 'content' ),
				);
			},
		) );
	}
}
