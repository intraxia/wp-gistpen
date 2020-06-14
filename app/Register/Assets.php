<?php
namespace Intraxia\Gistpen\Register;

use Intraxia\Gistpen\Model\Repo;
use Intraxia\Gistpen\Params\Repository as Params;
use Intraxia\Jaxion\Assets\Register;
use Intraxia\Jaxion\Core\Config;
use Psr\Container\ContainerInterface as Container;
use Exception;

/**
 * Class Register\Assets.
 *
 * @package    Intraxia\Gistpen
 * @subpackage Register
 */
class Assets {

	/**
	 * Configuration for each asset entrypoint.
	 *
	 * @var array
	 */
	private $asset_config = [];

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
		$this->container    = $container;
		$this->asset_config = [
			'edit'     => [
				'type'      => 'admin',
				'condition' => function () {
					$cond = get_current_screen()->id === 'gistpen';

					if ( $cond ) {
						wp_dequeue_script( 'autosave' );
					}

					return $cond;
				},
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
			],
			'content'  => [
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
			],
			'settings' => [
				'type'      => 'admin',
				'condition' => function () {
					return 'settings_page_wp-gistpen' === get_current_screen()->id;
				},
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
			],
			'block'    => [
				'type' => 'block',
			],
		];
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param Register $assets
	 * @throws Exception
	 */
	public function add_assets( Register $assets ) {
		/** App config. @var Config $config */
		$config         = $this->container->get( Config::class );
		$debug          = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
		$asset_manifest = $config->get_json_resource(
			'assets/asset-manifest' . ( $debug ? '' : '.min' )
		);
		$wp_assets      = $config->get_json_resource(
			'assets/wp-assets' . ( $debug ? '' : '.min' )
		);

		if ( null === $asset_manifest || null === $wp_assets ) {
			throw new Exception( 'Asset manifest or dependencies not found' );
		}

		foreach ( $asset_manifest['entrypoints'] as $entry => $files ) {
			$this->process_entrypoint( $assets, $entry, $files, $wp_assets[ $entry . '.js' ]['dependencies'] );
		}
	}

	/**
	 * Process a given entrypoint.
	 *
	 * @param Register $assets
	 * @param string   $entry
	 * @param array    $files
	 * @param array    $deps
	 * @throws Exception
	 */
	private function process_entrypoint( Register $assets, $entry, $files, $deps ) {
		// TinyMCE plugins are registered differently than regular assets.
		if ( 'tinymce' === $entry ) {
			return;
		}

		if ( ! isset( $this->asset_config[ $entry ] ) ) {
			throw new Exception( 'Unexpected entry in manifest: ' . $entry );
		}

		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
		/** App slug @var string $slug  */
		$slug         = $this->container->get( 'slug' );
		$asset_config = $this->asset_config[ $entry ];
		/** App config. @var Config $config */
		$config = $this->container->get( Config::class );
		$asset  = $config->get_json_resource(
			'assets/' . $entry . ( $debug ? '' : '.min' ) . '.asset'
		);

		foreach ( $files as $file ) {
			if ( false !== strpos( $file, '.js' ) ) {
				$assets->register_script( array(
					'type'      => $asset_config['type'],
					'deps'      => $deps,
					'condition' => isset( $asset_config['condition'] ) ? $asset_config['condition'] : null,
					'handle'    => $slug . '-' . $entry . '-script',
					'src'       => 'resources/assets/' . $file,
					'footer'    => true,
					'localize'  => isset( $asset_config['localize'] ) ? $asset_config['localize'] : null,
					'block'     => 'intraxia/gistpen',
				) );
			}

			if ( false !== strpos( $file, '.css' ) ) {
				$assets->register_style( array(
					'type'      => $asset_config['type'],
					'condition' => $asset_config['condition'],
					'handle'    => $slug . '-' . $entry . '-style',
					'src'       => 'resources/assets/' . $file,
				) );
			}
		}
	}
}
