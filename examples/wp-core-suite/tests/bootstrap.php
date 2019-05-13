<?php

require_once __DIR__ . '/../vendor/autoload.php';

/*
 * Init Function Mocker before WordPress to wrap its code.
 * Since WordPress will be loaded there is no need to load the WordPress environment.
 */
\tad\FunctionMocker\FunctionMocker::init([
    'whitelist'             => [
        __DIR__,
        dirname(__DIR__) . '/src',
        dirname(__DIR__) . '/vendor/wordpress/wordpress/src',
    ],
    'redefinable-internals' => [ 'time' ],
    'load-wp-env' => false
]);


// set some paths to get the things we need from the WordPress folder installed with Composer
$wp_dir = dirname(__DIR__) . '/vendor/wordpress/wordpress';
$tests_dir = $wp_dir . '/tests/phpunit';
$wp_tests_config_file = $wp_dir . '/wp-tests-config.php';

// if we did not already then place the tests config file in place
if (! file_exists($wp_tests_config_file)) {
    copy(__DIR__ . '/_support/wp-tests-config.php', $wp_tests_config_file);
}

// the `tests_add_filter` function is defined here
require_once $tests_dir . '/includes/functions.php';

// when WordPress is bootstrapped like this it will not load the plugins we need to manually include it
function load_plugin()
{
    include_once __DIR__ . '/../plugin.php';
}
tests_add_filter('muplugins_loaded', 'load_plugin');

// finally bootstrap WordPress
include $tests_dir . '/includes/bootstrap.php';
