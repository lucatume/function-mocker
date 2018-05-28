<?php
require_once __DIR__ . '/../vendor/autoload.php';

print_r( __DIR__ . '/_cache' );
\tad\FunctionMocker\FunctionMocker::init( [
	'cache-path'            => __DIR__ . '/_cache',
	'whitelist'             => [ __DIR__ ],
	'redefinable-internals' => [ 'time' ],
] );

foreach ( glob( __DIR__ . '/_data/*.php' ) as $file ) {
	include $file;
}

