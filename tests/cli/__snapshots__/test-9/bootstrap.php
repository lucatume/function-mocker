<?php
require_once __DIR__ . '/src/functions.php';
require_once __DIR__ . '/src/Acme/Company/functions.php';
require_once __DIR__ . '/TestEnvEnvAutoloader.php';

spl_autoload_register( [ new TestEnvEnvAutoloader( __DIR__ ), 'autoload' ] );