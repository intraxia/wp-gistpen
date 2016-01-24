<?php
namespace Intraxia\Gistpen\Providers;

use Intraxia\Gistpen\Model\Language;
use Intraxia\Gistpen\View\Editor;
use Intraxia\Jaxion\Assets\Register as Assets;
use Intraxia\Jaxion\Assets\ServiceProvider;

/**
 * Class AssetServiceProvider
 *
 * @package    Intraxia\Gistpen
 * @subpackage Providers
 */
class AssetsServiceProvider extends ServiceProvider {
	/**
	 * {@inheritDoc}
	 *
	 * @param Assets $assets
	 */
	protected function add_assets( Assets $assets ) {
		$debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;

		if ( $debug ) {
			$assets->set_debug( true );
		}

		$slug     = $this->container->fetch( 'slug' );
		$url      = $this->container->fetch( 'url' );

		$localize = function () use ( $url, $slug, $debug ) {
			$theme = cmb2_get_option( $slug, '_wpgp_gistpen_highlighter_theme' );

			return array(
				'name' => 'Gistpen_Settings',
				'data' => array(
					'debug'      => $debug,
					'languages'  => Language::$supported,
					'root'       => esc_url_raw( rest_url() . 'intraxia/v1/gistpen/' ),
					'nonce'      => wp_create_nonce( 'wp_rest' ),
					'url'        => $url,
					'ace_themes' => Editor::$ace_themes,
					'statuses'   => get_post_statuses(),
					'prism'      => array(
						'theme'   => $theme ? : 'default',
						'plugins' => array(
							'line-numbers'    => array( 'enabled' => 'on' === cmb2_get_option( $slug, '_wpgp_gistpen_line_numbers' ) ),
							'show-invisibles' => array( 'enabled' => 'on' === cmb2_get_option( $slug, '_wpgp_show_invisibles' ) ),
						),
					),
				),
			);
		};

		$should_enqueue_settings = function () {
			return 'settings_page_wp-gistpen' === get_current_screen()->id;
		};
		$should_enqueue_tinymce  = function () {
			return 'gistpen' !== get_current_screen()->id;
		};
		$should_enqueue_editor   = function () {
			if ( 'gistpen' === get_current_screen()->id ) {
				wp_dequeue_script( 'autosave' ); // @todo
				return true;
			}

			return false;
		};

		/**
		 * Ace Editor Scripts
		 */
		$assets->register_script( array(
			'type'      => 'admin',
			'condition' => function () use ( $should_enqueue_tinymce, $should_enqueue_editor ) {
				return $should_enqueue_tinymce() || $should_enqueue_editor();
			},
			'handle'    => $slug . '-ace-script',
			'src'       => 'assets/js/ace/ace',
			'localize'  => $localize,
		) );

		/**
		 * Post Editor Assets
		 */
		$assets->register_style( array(
			'type'      => 'admin',
			'condition' => $should_enqueue_editor,
			'handle'    => $slug . '-editor-styles',
			'src'       => 'assets/css/post',
		) );
		$assets->register_script( array(
			'type'      => 'admin',
			'condition' => $should_enqueue_editor,
			'handle'    => $slug . '-editor-script',
			'src'       => 'assets/js/post',
			'deps'      => array( $slug . '-ace-script' ), // @todo bundle Ace into the editor build
			'localize'  => $localize,
		) );

		/**
		 * Settings Page Assets
		 */
		$assets->register_style( array(
			'type'      => 'admin',
			'condition' => $should_enqueue_settings,
			'handle'    => $slug . '-settings-styles',
			'src'       => 'assets/css/settings',
		) );
		$assets->register_script( array(
			'type'      => 'admin',
			'condition' => $should_enqueue_settings,
			'handle'    => $slug . '-settings-script',
			'src'       => 'assets/js/settings',
			'footer'    => true,
			'localize'  => $localize,
		) );

		/**
		 * TinyMCE Popup Assets
		 */
		$assets->register_style( array(
			'type'      => 'admin',
			'condition' => $should_enqueue_tinymce,
			'handle'    => $slug . '-popup-styles',
			'src'       => 'assets/css/tinymce',
		) );

		/**
		 * Web Assets
		 */
		$assets->register_script( array(
			'type'      => 'web',
			'condition' => function () {
				return true;
			},
			'handle'    => $slug . '-web-script',
			'src'       => 'assets/js/web',
			'localize'  => $localize,
		) );
	}
}
