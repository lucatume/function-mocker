<?php



class EnvAutoloader_test_env {

	protected static $classMap = [
		'Acme\Company\FinalClass' =>  __DIR__ . '/Acme/Company/FinalClass.php',
	];

	protected $rootDir;

	public function __construct( $rootDir ) {
		$this->rootDir = $rootDir;
	}

	public function autoload( $class ) {
		if ( array_key_exists( $class, static::$classMap ) ) {
			include_once static::$classMap[ $class ];

			return true;
		}

		return false;
	}
}

spl_autoload_register( [ new EnvAutoloader_test_env( __DIR__ ), 'autoload' ] );