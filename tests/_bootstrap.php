<?php
require_once __DIR__ . '/../vendor/autoload.php';

\tad\FunctionMocker\FunctionMocker::init( [
	'cache-path'            => __DIR__ . '/../../_cache/fm-main',
	'whitelist'             => [ __DIR__ . '/FunctionMocker', __DIR__ . '/_data' ],
	'blacklist'             => [ __DIR__ . '/cli' ],
	'redefinable-internals' => [ 'time' ],
] );

foreach ( glob( __DIR__ . '/_data/*.php' ) as $file ) {
	include $file;
}

function _data_dir( $path = '' ) {
	return __DIR__ . '/_data/' . ltrim( $path, '/\\' );
}

function _output_dir( $path = '' ) {
	return __DIR__ . '/_output/' . ltrim( $path, '/\\' );
}

include_once __DIR__ . '/Traits/SnapshotAssertions.php';
