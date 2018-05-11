<?php
require_once  dirname(__DIR__) . '/vendor/autoload.php';

// init Function Mocker
\tad\FunctionMocker\FunctionMocker::init( [
	'cache-path'            => __DIR__ . '/_cache',
	'whitelist'             => [ __DIR__, dirname( __DIR__ ) . '/src' ],
	'redefinable-internals' => [ 'time' ],
] );

// setup some constants
if ( ! defined( 'DAY_IN_SECONDS' ) ) {
	define( 'DAY_IN_SECONDS', 24 * 60 * 60 );
}

// include the class to test
include_once dirname(__DIR__) . '/src/Logger.php';

