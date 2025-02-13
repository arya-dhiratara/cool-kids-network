<?php
/**
 * PHPUnit bootstrap file
 */

$_tests_dir = getenv('WP_TESTS_DIR') ?: '/Users/pitu/wordpress-tests/tests/phpunit';

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
    exit( "Could not find WordPress test environment.\n" );
}

require_once $_tests_dir . '/includes/functions.php';

function manually_load_plugin() {
    require dirname(__DIR__) . '/cool-kids-network.php';
}

tests_add_filter('muplugins_loaded', 'manually_load_plugin');

require $_tests_dir . '/includes/bootstrap.php';