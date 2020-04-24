<?php

/**
 * PHPUnit bootstrap file
 *
 * @package Intraxia\Gistpen\Test
 */

use Intraxia\Gistpen\Lifecycle;
use function Intraxia\Gistpen\container;

$plugin_root = dirname( dirname( __FILE__ ) );
$_tests_dir  = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';

	if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
		$_tests_dir = $plugin_root . '/wordpress/tests/phpunit';
	}
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL;
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
$_manually_load_plugin = function() use ( $plugin_root ) {
	create_if_missing(
		$plugin_root . '/resources/languages.json',
		[
			'list'    => [
				'js'        => 'JavaScript',
				'php'       => 'PHP',
				'plaintext' => 'PlainText',
			],
			'aliases' => [
				'js' => 'javascript',
			],
		]
	);

	$dummy_asset_manifest = [
		'entrypoints' => [],
	];

	create_if_missing(
		$plugin_root . '/resources/assets/asset-manifest.json',
		$dummy_asset_manifest
	);

	create_if_missing(
		$plugin_root . '/resources/assets/asset-manifest.min.json',
		$dummy_asset_manifest
	);

	require $plugin_root . '/wp-gistpen.php';

	container()->get( Lifecycle::class )->activate();
};

tests_add_filter( 'muplugins_loaded', $_manually_load_plugin );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';

function create_if_missing( $file, array $json ) {
	if ( ! file_exists( $file ) ) {
		file_force_contents( $file, wp_json_encode( $json ) );
	}
}

function file_force_contents( $dir, $contents ) {
	$parts = explode( '/', $dir );
	$file  = array_pop( $parts );
	$dir   = '';
	foreach ( $parts as $part ) {
		// @codingStandardsIgnoreLine
		if ( ! is_dir( $dir .= "/$part" ) ) {
			mkdir( $dir );
		}
	}
	file_put_contents( "$dir/$file", $contents );
}
