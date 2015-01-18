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
	 * Runs on plugin activation
	 *
	 * @since    0.5.0
	 */
	public static function activate() {

		if ( ! get_option( 'wp_gistpen_activate' ) ) {
			update_option( 'wp_gistpen_version', \WP_Gistpen::$version );
		}

		update_option( '_wpgp_activated', 'done' );
		flush_rewrite_rules( true );
	}

}
