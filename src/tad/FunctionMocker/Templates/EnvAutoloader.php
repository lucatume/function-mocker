<?php

namespace tad\FunctionMocker\Templates;

class EnvAutoloader extends Template {

	protected static $template = <<< PHP
{{{header}}}
/**
 * Class {{id}}
 *
 * Handles the autoloading of the {{name}} test environment classes.
 */
class {{id}} {

	/**
	 * A map of fully-qualified class names to their path.
	 * @var array 
	 */
	protected static \$classMap = [
	{{#each classMap}}
		'{{@key}}' =>  __DIR__ . '/{{this}}.php',
	{{/each}}
	];
	
	/**
	 * Finds and loads a class file managed by the autoloader.
	 * 
	 * @param string \$class The class fully qualified name.
	 *
	 * @return bool Whether the file for the class was found and
	 *              loaded or not.
	 */
	public function autoload( \$class ) {
		if ( array_key_exists( \$class, static::\$classMap ) ) {
			include_once static::\$classMap[ \$class ];

			return true;
		}

		return false;
	}
}

PHP;

	protected $extraLines = [ "spl_autoload_register( [ new {{id}}( __DIR__ ), 'autoload' ] );" ];

}
