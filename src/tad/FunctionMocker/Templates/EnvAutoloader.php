<?php
/**
 * Code template for the environment autoloader class.
 *
 * @package    FunctionMocker
 * @subpackage CLI
 * @author     Luca Tumedei <luca@theaveragedev.com>
 * @copyright  2018 Luca Tumedei
 */

namespace tad\FunctionMocker\Templates;

/**
 * Class EnvAutoloader
 */
class EnvAutoloader extends Template {

	/**
	 * The environment autoloader class code template, the `spl_autoload_register` call is in the extra lines.
	 * @var string
	 */
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
		'{{@key}}' =>  __DIR__ . '/src/{{this}}.php',
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

	/**
	 * Environment autoloader template extra lines define lines that will be included in the
	 * bootstrap file to register the autoloader itself.
	 *
	 * @var array
	 */
	protected static $extraLines = [ "spl_autoload_register( [ new {{id}}( __DIR__ ), 'autoload' ] );" ];

}
