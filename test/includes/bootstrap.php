<?php

$_tests_dir = getenv('WP_TESTS_DIR');
if (!$_tests_dir) {
    $_tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';

tests_add_filter('muplugins_loaded', function () {
    require dirname(__FILE__) . '/../../wp-gistpen.php';
});

require $_tests_dir . '/includes/bootstrap.php';
require_once dirname(__FILE__) . '/factory.php';
require_once dirname(__FILE__) . '/testcase.php';

Intraxia\Gistpen\App::get()->activate();
