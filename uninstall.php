<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * Currently inactive, no cleanup is required at this time.
 */
// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// @todo Add uninstallation steps (if we even need them).
