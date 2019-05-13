<?php
define('ABSPATH', __DIR__ . '/src/');
define('WP_DEFAULT_THEME', 'default');
define('WP_DEBUG', true);

require_once __DIR__ . '/../../../vendor/autoload.php';

$_tests_dir = __DIR__ . '/../../../tests';
if (file_exists($_tests_dir . '/.env.local')) {
    $env = new \Dotenv\Dotenv($_tests_dir, '.env.local');
} else {
    $env = new \Dotenv\Dotenv($_tests_dir, '.env');
}

$env->load();

$constants = [
    'DB_NAME',
    'DB_USER',
    'DB_PASSWORD',
    'DB_HOST',
    'DB_CHARSET',
    'DB_COLLATE',
    'AUTH_KEY',
    'SECURE_AUTH_KEY',
    'LOGGED_IN_KEY',
    'NONCE_KEY',
    'AUTH_SALT',
    'SECURE_AUTH_SALT',
    'LOGGED_IN_SALT',
    'NONCE_SALT',
    'WP_TESTS_DOMAIN',
    'WP_TESTS_EMAIL',
    'WP_TESTS_TITLE',
    'WP_PHP_BINARY',
    'WPLANG',
];

foreach ($constants as $constant) {
    define($constant, getenv($constant));
}

$table_prefix = 'test_';
