<?php
require_once( __DIR__ . '/../vendor/autoload.php' );

// init Function Mocker before WordPress to wrap its code
\tad\FunctionMocker\FunctionMocker::init( [
	'cache-path'            => __DIR__ . '/_cache',
	'whitelist'             => [
		__DIR__,
		dirname( __DIR__ ) . '/src',
		dirname( __DIR__ ) . '/vendor/wordpress/src',
	],
	'redefinable-internals' => [ 'time' ],
	'load-wp-env' => false
] );
