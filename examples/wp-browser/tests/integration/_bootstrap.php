<?php
// init Function Mocker
\tad\FunctionMocker\FunctionMocker::init( [
	'cache-path'            => __DIR__ . '/../../../../../_cache/fm-wpbrowser-example',
	'whitelist'             => [ __DIR__, dirname( __DIR__, 2 ) . '/src' ],
	'redefinable-internals' => [ 'time' ],
] );
