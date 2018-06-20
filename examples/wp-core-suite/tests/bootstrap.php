<?php
require_once( __DIR__ . '/../vendor/autoload.php' );

// init Function Mocker before WordPress to wrap its code
\tad\FunctionMocker\FunctionMocker::init( [
	'cache-path'            => __DIR__ .'/../../../../_cache/fm-coresuite-example',
	'whitelist'             => [
		__DIR__,
		dirname( __DIR__ ) . '/src',
		dirname( __DIR__ ) . '/vendor/wordpress/src',
	],
	'redefinable-internals' => [ 'time' ],
] );

// set some paths to get the things we need from the WordPress folder installed with Composer
$wp_dir = dirname( __DIR__ ) . '/vendor/wordpress';
$tests_dir = $wp_dir . '/tests/phpunit';
$wp_tests_config_file = $wp_dir . '/wp-tests-config.php';

// if we did not already then place the tests config file in place
if ( ! file_exists( $wp_tests_config_file ) ) {
	copy( __DIR__ . '/_support/wp-tests-config.php', $wp_tests_config_file );
}

// the `tests_add_filter` function is defined here
require_once $tests_dir . '/includes/functions.php';

// when WordPress is bootstrapped like this it will not load the plugins we need to manually include it
function load_plugin() {
	include_once __DIR__ . '/../plugin.php';
}
tests_add_filter( 'muplugins_loaded', 'load_plugin' );

// finally bootstrap WordPress
include $tests_dir . '/includes/bootstrap.php';

