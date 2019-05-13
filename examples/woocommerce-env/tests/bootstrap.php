<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

// init Function Mocker
\tad\FunctionMocker\FunctionMocker::init([
    'whitelist'             => [
        __DIR__,
         dirname(__DIR__) . '/src',
        __DIR__ . '/envs/woocommerce'
    ],
    'env' => [
        'WordPress',
        __DIR__ . '/envs/woocommerce/bootstrap.php',
    ],
]);
