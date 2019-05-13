<?php
/*
Plugin Name: Test Logger
Plugin URI: https://wordpress.org/plugins/
Author: Luca Tumedei
Author URI: https://theaveragedev.com
*/

require_once __DIR__ . '/vendor/autoload.php';

function logger_start()
{
    if (! is_admin()) {
        return;
    }

    \Examples\WPBrowser\Logger::start();
}

add_action('init', 'logger_start');
