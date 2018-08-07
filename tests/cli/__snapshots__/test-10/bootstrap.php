<?php



class EnvAutoloader_test_env {

	protected static $classMap = [
		'GlobalAbstractClass' =>  __DIR__ . '/GlobalAbstractClass.php',
		'Acme\Company\NamespacedAbstractClass' =>  __DIR__ . '/Acme/Company/NamespacedAbstractClass.php',
		'GlobalInterface' =>  __DIR__ . '/GlobalInterface.php',
		'Acme\Company\NamespacedInterface' =>  __DIR__ . '/Acme/Company/NamespacedInterface.php',
		'GlobalTrait' =>  __DIR__ . '/GlobalTrait.php',
		'Acme\Company\NamespacedTrait' =>  __DIR__ . '/Acme/Company/NamespacedTrait.php',
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