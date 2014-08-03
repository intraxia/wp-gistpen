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
 * Version:           0.3.0
 * Author:            James DiGioia
 * Author URI:        http://www.jamesdigioia.com/
 * Text Domain:       wp-gistpen
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/maadhattah/wp-gistpen
 * WordPress-Plugin-Boilerplate: v2.6.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Define Directory Constants
 *----------------------------------------------------------------------------*/

// Directory i.e. /home/user/public_html...
define('WP_GISTPEN_DIR', plugin_dir_path( __FILE__ ));
// URL i.e. http://www.yoursite/wp-content/plugins/wp-gistpen/
define('WP_GISTPEN_URL', plugin_dir_url( __FILE__ ));

/*----------------------------------------------------------------------------*
 * Load Helper Classes
 * @todo Write proper autoloader or swap to composer
 *----------------------------------------------------------------------------*/

require_once( WP_GISTPEN_DIR . 'helpers/class-wp-gistpen-updater.php' );
require_once( WP_GISTPEN_DIR . 'helpers/class-wp-gistpen-content.php' );

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

/**
 * Load the plugin class file
 */
require_once( WP_GISTPEN_DIR . 'public/class-wp-gistpen.php' );

/**
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'WP_Gistpen', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WP_Gistpen', 'deactivate' ) );

/**
 * Load an instance of the plugin class object
 */
add_action( 'plugins_loaded', array( 'WP_Gistpen', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/**
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() ) {
	require_once( WP_GISTPEN_DIR . 'admin/class-wp-gistpen-admin.php' );
	add_action( 'plugins_loaded', array( 'WP_Gistpen_Admin', 'get_instance' ) );
	require_once( WP_GISTPEN_DIR . 'admin/class-wp-gistpen-editor.php' );
	add_action( 'plugins_loaded', array( 'WP_Gistpen_Editor', 'get_instance' ) );
}
