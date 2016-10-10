<?php
namespace Intraxia\Gistpen\Providers;

use Intraxia\Gistpen\Model\Language;
use Intraxia\Gistpen\View\Content;
use Intraxia\Gistpen\View\Editor;
use Intraxia\Gistpen\View\Settings;
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

		$slug = $this->container->fetch( 'slug' );

		$should_enqueue_settings = function () {
			return 'settings_page_wp-gistpen' === get_current_screen()->id;
		};
		$should_enqueue_tinymce  = function () {
			return 'gistpen' !== get_current_screen()->id;
		};

		/**
		 * Editor Assets
		 */
		$assets->register_script( array(
			'type'      => 'admin',
			'condition' => function () {
				return 'gistpen' === get_current_screen()->id;
			},
			'handle'    => $slug . '-editor-script',
			'src'       => 'assets/js/editor',
			'localize'  => function () {
				/** @var Editor $editor */
				$editor = $this->container->fetch( 'view.editor' );

				return array(
					'name' => '__GISTPEN_SETTINGS__',
					'data' => $editor->get_initial_state(),
				);
			},
		) );

		/**
		 * Settings Page Assets
		 */
		$assets->register_script( array(
			'type'      => 'admin',
			'condition' => $should_enqueue_settings,
			'handle'    => $slug . '-settings-script',
			'src'       => 'assets/js/settings',
			'footer'    => true,
			'localize'  => function () {
				/** @var Settings $settings */
				$settings = $this->container->fetch( 'view.settings' );

				return array(
					'name' => '__GISTPEN_SETTINGS__',
					'data' => $settings->get_initial_state(),
				);
			},
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
		 * Content Assets
		 */
		$assets->register_script( array(
			'type'      => 'web',
			'condition' => function () {
				return true;
			},
			'handle'    => $slug . '-content-script',
			'src'       => 'assets/js/content',
			'footer'    => false,
			'localize'  => function() {
				/** @var Content $content */
				$content = $this->container->fetch( 'view.content' );

				return array(
					'name' => '__GISTPEN_CONTENT__',
					'data' => $content->get_initial_state(),
				);
			},
		) );
	}
}
