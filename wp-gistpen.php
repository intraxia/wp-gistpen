<?php
/**
 * WP-Gistpen
 *
 * A self-hosted alternative to putting your code snippets on Gist.
 *
 * @package   WP_Gistpen
 * @author    James DiGioia <jamesorodig@gmail.com>
 * @license   GPL-2.0+
 * @link      http://jamesdigioia.com/wp-gistpen/
 * @copyright 2014 James DiGioia
 *
 * @wordpress-plugin
 * Plugin Name:       WP-Gistpen
 * Plugin URI:        http://www.jamesdigioia.com/wp-gistpen/
 * Description:       A self-hosted alternative to putting your code snippets on Gist.
 * Version:           0.5.8
 * Author:            James DiGioia
 * Author URI:        http://www.jamesdigioia.com/
 * Text Domain:       wp-gistpen
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/maadhattah/wp-gistpen
 * WordPress-Plugin-Boilerplate: v3.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Define Constants
 *----------------------------------------------------------------------------*/

// Directory i.e. /home/user/public_html...
define( 'WP_GISTPEN_DIR', plugin_dir_path( __FILE__ ) );
// URL i.e. http://www.yoursite/wp-content/plugins/wp-gistpen/
define( 'WP_GISTPEN_URL', plugin_dir_url( __FILE__ ) );
// Plugin Basename, for settings page
define( 'WP_GISTPEN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Include the autoloader
 */
require_once 'lib/autoload.php';

/** This action is documented in app/Activator.php */
register_activation_hook( __FILE__, array( 'WP_Gistpen\Activator', 'activate' ) );

/** This action is documented in app/Deactivator.php */
register_deactivation_hook( __FILE__, array( 'WP_Gistpen\Deactivator', 'deactivate' ) );

/**
 * Singleton container class
 */
class WP_Gistpen {

	public static $app;

	public static $plugin_name = 'wp-gistpen';

	public static $version = '0.5.8';

	public static function init() {

		if ( null == self::$app ) {
			self::$app = new WP_Gistpen\App( self::$plugin_name, self::$version );
			self::$app->run();
		}

		return self::$app;
	}
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * Also returns copy of the app object so 3rd party developers
 * can interact with the app's hooks contained within.
 *
 * @since    0.5.0
 */
function wp_gistpen() {
	return WP_Gistpen::init();
}

$updatePhp = new WPUpdatePhp( '5.3.0' );

if ( $updatePhp->does_it_meet_required_php_version( PHP_VERSION ) ) {
	wp_gistpen();
}
