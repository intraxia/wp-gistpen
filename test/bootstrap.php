<?php

/**
 * PHPUnit bootstrap file
 *
 * @package Intraxia\Gistpen\Test
 */

use Intraxia\Gistpen\App;

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL;
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

$plugin_root = dirname( dirname( __FILE__ ) );

/**
 * Manually load the plugin being tested.
 */
$_manually_load_plugin = function() use ( $plugin_root ) {
	require $plugin_root . '/wp-gistpen.php';
};

tests_add_filter( 'muplugins_loaded', $_manually_load_plugin );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';

$languages_config = $plugin_root . '/config/languages.json';

$exists = file_exists( $languages_config );

if ( ! $exists ) {
	$dummy = array(
		'list' =>  array(
			'js'  => 'JavaScript',
			'php' => 'PHP',
		),
		'aliases' => array(
			'js'  => 'javascript',
		),
	);

	file_force_contents( $languages_config, json_encode( $dummy ) );
}

App::instance()->activate();

function file_force_contents( $dir, $contents ) {
	$parts = explode( '/', $dir );
	$file = array_pop( $parts );
	$dir = '';
	foreach( $parts as $part ) {
		if( ! is_dir( $dir .= "/$part" ) ) {
			mkdir($dir);
		}
	}
	file_put_contents("$dir/$file", $contents);
}
