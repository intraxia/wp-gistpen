<?php
namespace WP_Gistpen;

use WP_Gistpen\Model\Language;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @package    WP_Gistpen
 * @author     James DiGioia <jamesorodig@gmail.com>
 * @link       http://jamesdigioia.com/wp-gistpen/
 * @since      0.5.0
 */
class Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    0.5.0
	 */
	public static function activate() {
		// @todo we don't want to redefine this here if we can help it
		$register = new Register\Data( \WP_Gistpen::$plugin_name, \WP_Gistpen::$version );
		$register->language_tax();
		self::add_languages();
		// @todo or here
		// update_option( 'wp_gistpen_version', self::VERSION );
		flush_rewrite_rules( true );
	}

	/**
	 * Create the languages
	 *
	 * @since    0.1.0
	 */
	public static function add_languages() {

		// note to self: delete this line in version 0.4.0
		delete_option( 'wp_gistpen_langs_installed' );

		if ( true === get_option( 'wp_gistpens_languages_installed') ) {
			return;
		}

		foreach( Language::$supported as $lang => $slug ) {
			$result = wp_insert_term( $lang, 'wpgp_language', array( 'slug' => $slug ) );
			if( is_wp_error( $result ) ) {
				// @todo write error message?
			}
		}

		update_option( 'wp_gistpens_languages_installed', true );

	}

}
