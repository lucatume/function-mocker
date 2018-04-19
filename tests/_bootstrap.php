<?php
require_once __DIR__ . '/../vendor/autoload.php';

\tad\FunctionMocker\FunctionMocker::init( [
	'whitelist' => [__DIR__],
] );

foreach ( glob( __DIR__ . '/_data/*.php' ) as $file ) {
	include $file;
}

