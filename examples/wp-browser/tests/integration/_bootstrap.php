<?php
// init Function Mocker
\tad\FunctionMocker\FunctionMocker::init( [
	'whitelist'             => [ __DIR__, dirname( __DIR__, 2 ) . '/src' ],
	'redefinable-internals' => [ 'time' ],
	'load-wp-env' => false
] );
