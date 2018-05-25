<?php
/*
Plugin Name: Test Logger
Plugin URI: https://wordpress.org/plugins/
Author: Luca Tumedei
Author URI: https://theaveragedev.com
*/

require_once __DIR__ . '/vendor/autoload.php';

function logger_start() {
	if ( ! is_admin() ) {
		return;
	}

	\Examples\WPCore\Logger::start();
}

add_action('init', 'logger_start');
add_action('shutdown', 'logger_stop');