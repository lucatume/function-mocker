<?php

namespace tad\FunctionMocker\Templates;

class EnvAutoloader extends Template
{

	protected static $template = <<< PHP
class EnvAutoloader_{{id}} {

	protected static \$classMap = [
	{{#each classMap}}
		'{{@key}}' =>  __DIR__ . '/{{this}}.php',
	{{/each}}
	];

	protected \$rootDir;

	public function __construct( \$rootDir ) {
		\$this->rootDir = \$rootDir;
	}

	public function autoload( \$class ) {
		if ( array_key_exists( \$class, static::\$classMap ) ) {
			include_once static::\$classMap[ \$class ];

			return true;
		}

		return false;
	}
}

spl_autoload_register( [ new EnvAutoloader_{{id}}( __DIR__ ), 'autoload' ] );
PHP;
}
