<?php
namespace WP_Gistpen\Register;

use WP_Gistpen\Model\Language;

class Assets extends \Intraxia\Jaxion\Register\Assets {

	/**
	 * @inheritdoc
	 */
	public function register() {
		/**
		 * Settings Page Assets
		 */
		$settings_condition = function() {
			return 'settings_page_wp-gistpen' === get_current_screen()->id;
		};
		$this->registerStyle( array(
			'type' => 'admin',
			'condition' => $settings_condition,
			'handle' => \WP_Gistpen::$plugin_name .'-settings-styles',
			'src' => 'assets/css/settings',
		) );
		$this->registerScript( array(
			'type' => 'admin',
			'condition' => $settings_condition,
			'handle' => \WP_Gistpen::$plugin_name . '-settings-script',
			'src' => 'assets/js/settings',
			'deps' => array( 'jquery', 'jquery-ui-progressbar', 'ajaxq', 'backbone', 'underscore', \WP_Gistpen::$plugin_name . '-prism' ),
			'footer' => true,
			'localize' => array(
				'name' => 'WP_GISTPEN_URL',
				'data' => $this->url,
			),
		) );

		/**
		 * TinyMCE Popup Assets
		 */
		$popup_condition = function() {
			return 'post' === get_current_screen()->id || 'page' === get_current_screen()->id;
		};
		$this->registerStyle( array(
			'type' => 'admin',
			'condition' => $popup_condition,
			'handle' => \WP_Gistpen::$plugin_name .'-popup-styles',
			'src' => 'assets/css/popup',
		) );
		$this->registerScript( array(
			'type' => 'admin',
			'condition' => $popup_condition,
			'handle' => \WP_Gistpen::$plugin_name . '-popups-script',
			'src' => 'assets/js/popup',
			'deps' => array( 'jquery', \WP_Gistpen::$plugin_name . '-ace-script' ),
			'localize' => array(
				'name' => 'gistpenLanguages',
				'data' => Language::$supported,
			),
		) );

		/**
		 * Gistpen Editor Assets
		 */
		$editor_condition = function() {
			if ( 'gistpen' === get_current_screen()->id ) {
				wp_dequeue_script( 'autosave' ); // @todo
				return true;
			}

			return false;
		};
		$this->registerStyle( array(
			'type' => 'admin',
			'condition' => $editor_condition,
			'handle' => \WP_Gistpen::$plugin_name .'-editor-styles',
			'src' => 'assets/css/editor',
		) );
		$this->registerScript( array(
			'type' => 'admin',
			'condition' => $editor_condition,
			'handle' => \WP_Gistpen::$plugin_name . '-editor-script',
			'src' => 'assets/js/editor',
			'deps' => array( 'ajaxq', 'backbone', 'underscore', \WP_Gistpen::$plugin_name . '-ace-script' ),
			'localize' => array(
				'name' => 'gistpenLanguages',
				'data' => Language::$supported,
			),
		) );

		/**
		 * Prism Assets
		 */
		$prism_condition = function() {
			if ( ! is_admin() || 'settings_page_wp-gistpen' === get_current_screen()->id ) {
				return true;
			}

			return false;
		};
		$this->registerStyle( array(
			'type' => 'shared',
			'condition' => $prism_condition,
			'handle' => \WP_Gistpen::$plugin_name .'-prism-theme',
			'src' => 'assets/css/prism/themes/prism' . $this->get_prism_theme(),
		) );
		$this->registerStyle( array(
			'type' => 'shared',
			'condition' => $prism_condition,
			'handle' => \WP_Gistpen::$plugin_name .'-prism-line-highlight',
			'src' => 'assets/css/prism/plugins/line-highlight/prism-line-highlight',
			'deps' => array( \WP_Gistpen::$plugin_name . '-prism-theme' ),
		) );
		$this->registerStyle( array(
			'type' => 'shared',
			'condition' => function() use ($prism_condition) {
				return $prism_condition() && ( is_admin() ||  'on' === cmb2_get_option( \WP_Gistpen::$plugin_name, '_wpgp_gistpen_line_numbers' ) );
			},
			'handle' => \WP_Gistpen::$plugin_name .'-prism-line-numbers',
			'src' => 'assets/css/prism/plugins/line-numbers/prism-line-numbers',
			'deps' => array( \WP_Gistpen::$plugin_name . '-prism-theme' ),
		) );
		$this->registerScript( array(
			'type' => 'shared',
			'condition' => $prism_condition,
			'handle' => \WP_Gistpen::$plugin_name .'-prism',
			'src' => 'assets/js/prism',
			'deps' => array( 'jquery' ),
		) );

		/**
		 * Web Assets
		 */
		$this->registerStyle( array(
			'type' => 'web',
			'condition' => function() { return true; },
			'handle' => \WP_Gistpen::$plugin_name .'-web-styles',
			'src' => 'assets/css/web',
		) );

		/**
		 * Shared Libraries
		 */
		$this->registerScript( array(
			'type' => 'admin',
			'condition' => function() use ($settings_condition, $editor_condition) {
				return $settings_condition() || $editor_condition();
			},
			'handle' => 'ajaxq',
			'src' => 'assets/js/ajaxq',
			'deps' => array( 'jquery' ),
			'footer' => true,
		) );
		$this->registerScript( array(
			'type' => 'admin',
			'condition' => function() use ($popup_condition, $editor_condition) {
				return $popup_condition() || $editor_condition();
			},
			'handle' => \WP_Gistpen::$plugin_name . '-ace-script',
			'src' => 'assets/js/ace/ace',
		) );
	}

	/**
	 * Retrieve the Prism theme
	 *
	 * @return string Prism theme
	 * @since    0.5.0
	 */
	protected function get_prism_theme() {
		$theme = cmb2_get_option( \WP_Gistpen::$plugin_name, '_wpgp_gistpen_highlighter_theme' );

		if ( '' === $theme || 'default' === $theme ) {
			$theme = '';
		} else {
			$theme = '-' . $theme;
		}

		return $theme;
	}

}
