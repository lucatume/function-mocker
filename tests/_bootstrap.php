<?php
require_once __DIR__ . '/../vendor/autoload.php';

\tad\FunctionMocker\FunctionMocker::init( [
	'whitelist'             => [ __DIR__ . '/FunctionMocker', __DIR__ . '/_data' ],
	'blacklist'             => [ __DIR__ . '/cli' ],
	'redefinable-internals' => [ 'time' ],
] );

$dataFiles = new CallbackFilterIterator( new DirectoryIterator( __DIR__ . '/_data' ), function ( SplFileInfo $file ) {
	return $file->getExtension() === 'php';
} );
/** @var \SplFileInfo $file */
foreach ( $dataFiles as $file ) {
	include $file->getPathname();
}

function _data_dir( $path = '' ) {
	return __DIR__ . '/_data/' . ltrim( $path, '/\\' );
}

function _output_dir( $path = '' ) {
	return __DIR__ . '/_output/' . ltrim( $path, '/\\' );
}

include_once __DIR__ . '/Traits/SnapshotAssertions.php';
