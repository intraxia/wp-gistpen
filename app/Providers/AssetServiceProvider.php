<?php
namespace Intraxia\Gistpen\Providers;

use Intraxia\Gistpen\Model\Language;
use Intraxia\Jaxion\Assets\Register;
use Intraxia\Jaxion\Assets\ServiceProvider;

/**
 * Class AssetServiceProvider
 *
 * @package    Intraxia\Gistpen
 * @subpackage Providers
 */
class AssetServiceProvider extends ServiceProvider {
	/**
	 * {@inheritDoc}
	 *
	 * @param Register $register
	 */
	protected function add_assets( Register $register ) {
		$slug     = $this->container->fetch( 'slug' );
		$url = $this->container->fetch( 'url' );
		$localize = function() use ( $url ) {
			return array(
				'name' => 'Gistpen_Settings',
				'data' => array(
					'languages' => Language::$supported,
					'root'      => esc_url_raw( rest_url() ),
					'nonce'     => wp_create_nonce( 'wp_rest' ),
					'url'       => $url,
				),
			);
		};

		$settings_condition = function () {
			return 'settings_page_wp-gistpen' === get_current_screen()->id;
		};
		$popup_condition    = function () {
			return 'post' === get_current_screen()->id || 'page' === get_current_screen()->id;
		};
		$editor_condition   = function () {
			if ( 'gistpen' === get_current_screen()->id ) {
				wp_dequeue_script( 'autosave' ); // @todo
				return true;
			}

			return false;
		};

		/**
		 * Shared Libraries
		 */
		$register->register_script( array(
			'type'      => 'admin',
			'condition' => function () use ( $settings_condition, $editor_condition ) {
				return $settings_condition() || $editor_condition();
			},
			'handle'    => 'ajaxq',
			'src'       => 'assets/js/ajaxq',
			'deps'      => array( 'jquery' ),
			'footer'    => true,
		) );
		$register->register_script( array(
			'type'      => 'admin',
			'condition' => function () use ( $popup_condition, $editor_condition ) {
				return $popup_condition() || $editor_condition();
			},
			'handle'    => $slug . '-ace-script',
			'src'       => 'assets/js/ace/ace',
		) );

		/**
		 * Settings Page Assets
		 */
		$register->register_style( array(
			'type'      => 'admin',
			'condition' => $settings_condition,
			'handle'    => $slug . '-settings-styles',
			'src'       => 'assets/css/settings',
		) );
		$register->register_script( array(
			'type'      => 'admin',
			'condition' => $settings_condition,
			'handle'    => $slug . '-settings-script',
			'src'       => 'assets/js/settings',
			'deps'      => array(
				'jquery',
				'jquery-ui-progressbar',
				'ajaxq',
				'backbone',
				'underscore',
				$slug . '-prism'
			),
			'footer'    => true,
			'localize'  => $localize,
		) );

		/**
		 * TinyMCE Popup Assets
		 */
		$register->register_style( array(
			'type'      => 'admin',
			'condition' => $popup_condition,
			'handle'    => $slug . '-popup-styles',
			'src'       => 'assets/css/popup',
		) );
		$register->register_script( array(
			'type'      => 'admin',
			'condition' => $popup_condition,
			'handle'    => $slug . '-popups-script',
			'src'       => 'assets/js/popup',
			'deps'      => array( 'jquery', $slug . '-ace-script' ),
			'localize'  => $localize,
		) );

		/**
		 * Gistpen Editor Assets
		 */
		$register->register_style( array(
			'type'      => 'admin',
			'condition' => $editor_condition,
			'handle'    => $slug . '-editor-styles',
			'src'       => 'assets/css/editor',
		) );
		$register->register_script( array(
			'type'      => 'admin',
			'condition' => $editor_condition,
			'handle'    => $slug . '-editor-script',
			'src'       => 'assets/js/editor',
			'deps'      => array(
				'ajaxq',
				'backbone',
				'underscore',
				$slug . '-ace-script',
				'wp-api', // @todo remove? this only comes from the plugin currently
			),
			'localize'  => $localize,
		) );

		/**
		 * Prism Assets
		 */
		$prism_condition = function () {
			if ( ! is_admin() || 'settings_page_wp-gistpen' === get_current_screen()->id ) {
				return true;
			}

			return false;
		};
		$register->register_style( array(
			'type'      => 'shared',
			'condition' => $prism_condition,
			'handle'    => $slug . '-prism-theme',
			'src'       => 'assets/css/prism/themes/prism', // @todo . $this->get_prism_theme(),
		) );
		$register->register_style( array(
			'type'      => 'shared',
			'condition' => $prism_condition,
			'handle'    => $slug . '-prism-line-highlight',
			'src'       => 'assets/css/prism/plugins/line-highlight/prism-line-highlight',
			'deps'      => array( $slug . '-prism-theme' ),
		) );
		$register->register_style( array(
			'type'      => 'shared',
			'condition' => function () use ( $prism_condition, $slug ) {
				return $prism_condition() && (
					is_admin() ||
					'on' === cmb2_get_option( $slug, '_wpgp_gistpen_line_numbers' )
				);
			},
			'handle'    => $slug . '-prism-line-numbers',
			'src'       => 'assets/css/prism/plugins/line-numbers/prism-line-numbers',
			'deps'      => array( $slug . '-prism-theme' ),
		) );
		$register->register_script( array(
			'type'      => 'shared',
			'condition' => $prism_condition,
			'handle'    => $slug . '-prism',
			'src'       => 'assets/js/prism',
			'deps'      => array( 'jquery' ),
		) );

		/**
		 * Web Assets
		 */
		$register->register_style( array(
			'type'      => 'web',
			'condition' => function () {
				return true;
			},
			'handle'    => $slug . '-web-styles',
			'src'       => 'assets/css/web',
		) );
	}

	/**
	 * Retrieve the Prism theme
	 *
	 * @return string Prism theme
	 * @since    0.5.0
	 */
	private function get_prism_theme( $slug ) {
		$theme = cmb2_get_option( $slug, '_wpgp_gistpen_highlighter_theme' );

		if ( '' === $theme || 'default' === $theme ) {
			$theme = '';
		} else {
			$theme = '-' . $theme;
		}

		return $theme;
	}
}
