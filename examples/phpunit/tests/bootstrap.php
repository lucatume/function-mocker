<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Init Function Mocker.
\tad\FunctionMocker\FunctionMocker::init([
    'whitelist'             => [ __DIR__, dirname(__DIR__) . '/src' ],
    'redefinable-internals' => [ 'time' ],
]);

// Setup some constants WordPress would set up for us.
if (! defined('DAY_IN_SECONDS')) {
    define('DAY_IN_SECONDS', 24 * 60 * 60);
}
