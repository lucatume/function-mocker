<?php

/**
 * Class TestEnvEnvAutoloader
 *
 * Handles the autoloading of the  test environment classes.
 */
class TestEnvEnvAutoloader {

	/**
	 * A map of fully-qualified class names to their path.
	 * @var array 
	 */
	protected static $classMap = [
		'Acme\Company\ClassWPrivateMethods' =>  __DIR__ . '/src/Acme/Company/ClassWPrivateMethods.php',
		'Acme\Company\ClassWFinalMethods' =>  __DIR__ . '/src/Acme/Company/ClassWFinalMethods.php',
	];
	
	/**
	 * Finds and loads a class file managed by the autoloader.
	 * 
	 * @param string $class The class fully qualified name.
	 *
	 * @return bool Whether the file for the class was found and
	 *              loaded or not.
	 */
	public function autoload( $class ) {
		if ( array_key_exists( $class, static::$classMap ) ) {
			include_once static::$classMap[ $class ];

			return true;
		}

		return false;
	}
}
