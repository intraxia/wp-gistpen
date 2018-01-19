<?php
namespace Intraxia\Gistpen\Providers;

use Intraxia\Gistpen\Model\Repo;
use Intraxia\Gistpen\Params\Repository as Params;
use Intraxia\Gistpen\View\Edit;
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
		$assets->set_debug( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG );

		$slug = $this->container->fetch( 'slug' );

		/**
		 * Edit Assets
		 */
		$assets->register_script( array(
			'type'      => 'admin',
			'condition' => function () {
				$cond = 'gistpen' === get_current_screen()->id;

				if ( $cond ) {
					wp_dequeue_script( 'autosave' );
				}

				return $cond;
			},
			'handle'    => $slug . '-editor-script',
			'src'       => 'assets/js/editor',
			'footer'    => false,
			'localize'  => function () {
				/** @var Params $settings */
				$params = $this->container->fetch( 'params' );

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
			'condition' => function () {
				return 'settings_page_wp-gistpen' === get_current_screen()->id;
			},
			'handle'    => $slug . '-settings-script',
			'src'       => 'assets/js/settings',
			'footer'    => false,
			'localize'  => function () {
				/** @var Params $settings */
				$params = $this->container->fetch( 'params' );

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

				return has_shortcode( get_post()->post_content, 'gistpen' );
			},
			'handle'    => $slug . '-content-script',
			'src'       => 'assets/js/content',
			'footer'    => true,
			'localize'  => function() {
				/** @var Params $content */
				$params= $this->container->fetch( 'params' );

				return array(
					'name' => '__GISTPEN_CONTENT__',
					'data' => $params->state( 'content' ),
				);
			},
		) );
	}
}
