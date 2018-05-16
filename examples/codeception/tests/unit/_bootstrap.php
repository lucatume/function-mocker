<?php
require_once dirname( __DIR__, 2 ) . '/vendor/autoload.php';

// init Function Mocker
\tad\FunctionMocker\FunctionMocker::init( [
	'cache-path'            => dirname( __DIR__ ) . '/_cache',
	'whitelist'             => [ __DIR__, dirname( __DIR__, 2 ) . '/src' ],
	'redefinable-internals' => [ 'time' ],
] );

// setup some constants
if ( ! defined( 'DAY_IN_SECONDS' ) ) {
	define( 'DAY_IN_SECONDS', 24 * 60 * 60 );
}

