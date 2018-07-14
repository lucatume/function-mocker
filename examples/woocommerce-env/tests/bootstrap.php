<?php
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

// init Function Mocker
\tad\FunctionMocker\FunctionMocker::init( [
	'cache-path'            => __DIR__ . '/../../../../_cache/fm-woocommerce-env-example',
	'whitelist'             => [ __DIR__, dirname( __DIR__ ) . '/src' ],
] );

