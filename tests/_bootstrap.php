<?php
require_once __DIR__ . '/../vendor/autoload.php';

\tad\FunctionMocker\FunctionMocker::init( [
	'cache-path'            => getcwd() . '/../_cache/fm-main',
	'whitelist'             => [ __DIR__ ],
	'redefinable-internals' => [ 'time' ],
] );

foreach ( glob( __DIR__ . '/_data/*.php' ) as $file ) {
	include $file;
}

