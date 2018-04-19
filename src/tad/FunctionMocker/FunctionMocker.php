<?php

namespace tad\FunctionMocker;

use Prophecy\Prophecy\MethodProphecy;
use Prophecy\Prophecy\ProphecyInterface;
use Prophecy\Prophet;

class FunctionMocker {

	/**
	 * @var \tad\FunctionMocker\FunctionMocker
	 */
	protected static $instance;

	/** @var  bool */
	protected $didInit = false;

	/**
	 * @var object[]
	 */
	protected $revealed = [];

	/**
	 * @var ProphecyInterface[]
	 */
	protected $prophecies;

	/**
	 * @var \Prophecy\Prophet
	 */
	private $prophet;

	/**
	 * FunctionMocker constructor.
	 *
	 * @param \Prophecy\Prophet $prophet
	 */
	public function __construct( Prophet $prophet ) {
		$this->prophet = $prophet;
	}

	/**
	 * Loads Patchwork, use in setUp method of the test case.
	 *
	 * @return void
	 */
	public static function setUp() {
		if ( ! self::$didInit ) {
			self::init();
		}
	}

	/**
	 * Inits the mocking engine including the Patchwork library.
	 *
	 * @param array|null $options An array of options to init the Patchwork library.
	 *                            ['include'|'whitelist']     array|string A list of absolute paths that should be included in the patching.
	 *                            ['exclude'|'blacklist']     array|string A list of absolute paths that should be excluded in the patching.
	 *                            ['cache-path']              string The absolute path to the folder where Patchwork should cache the wrapped files.
	 *                            ['redefinable-internals']   array A list of internal PHP functions that are available for replacement.
	 *
	 * @param bool       $forceReinit
	 *
	 * @see \Patchwork\configure()
	 */
	public static function init( array $options = null, $forceReinit = false ) {
		if ( ! $forceReinit && self::instance()->didInit ) {
			return;
		}

		$packageRoot = dirname( __DIR__, 3 );

		static::instance()->writePatchworkConfig( $options, $packageRoot );

		/** @noinspection PhpIncludeInspection */
		Utils::includePatchwork();

		require_once dirname( __DIR__, 2 ) . '/utils.php';

		static::instance()->didInit = true;
	}

	public static function instance( FunctionMocker $instance = null ): FunctionMocker {
		if ( static::$instance === null ) {
			static::$instance = $instance ?? new static( new Prophet() );
		}

		return static::$instance;
	}

	/**
	 * Writes Patchwork configuration to file if needed.
	 *j
	 *
	 * @param array   $options           An array of options as those supported by Patchwork configuration.
	 * @param  string $destinationFolder The absolute path to the folder that will contain the cache folder and the Patchwork
	 *                                   configuration file.
	 *
	 * @return bool Whether the configuration file was written or not.
	 *
	 * @throws \RuntimeException If the Patchwork configuration file or the checksum file could not be written.
	 */
	public function writePatchworkConfig( array $options = null, $destinationFolder ) {
		$options = static::instance()->getPatchworkConfiguration( $options, $destinationFolder );

		$configFileContents = json_encode( $options );
		$configChecksum     = md5( $configFileContents );
		$configFilePath     = $destinationFolder . '/patchwork.json';
		$checksumFilePath   = "{$destinationFolder}/pw-cs-{$configChecksum}.yml";

		if ( file_exists( $configFilePath ) && file_exists( $checksumFilePath ) ) {
			return false;
		}

		if ( false === file_put_contents( $configFilePath, $configFileContents ) ) {
			throw new \RuntimeException( "Could not write Patchwork library configuration file to {$configFilePath}" );
		}

		foreach ( glob( $destinationFolder . '/pw-cs-*.yml' ) as $file ) {
			unlink( $file );
		}

		$date                 = date( 'Y-m-d H:i:s' );
		$checksumFileContents = <<< YAML
generator: FunctionMocker
date: $date
checksum: $configChecksum
for: $configFilePath
YAML;

		if ( false === file_put_contents( $checksumFilePath, $checksumFileContents ) ) {
			throw new \RuntimeException( "Could not write Patchwork library configuration checksum file to {$checksumFilePath}" );
		}

		return true;
	}

	/**
	 * Return the Patchwork configuration that should be written to file.
	 *
	 * @param array   $options           An array of options as those supported by Patchwork configuration.
	 * @param  string $destinationFolder The absolute path to the folder that will contain the cache folder and the Patchwork
	 *                                   configuration file.
	 *
	 * @return array
	 */
	public function getPatchworkConfiguration( $options = [], $destinationFolder ) {
		$translatedFields = [ 'include' => 'whitelist', 'exclude' => 'blacklist' ];

		foreach ( $translatedFields as $from => $to ) {
			if ( ! empty( $options[ $from ] ) && empty( $options[ $to ] ) ) {
				$options[ $to ] = $options[ $from ];
			}
			unset( $options[ $from ] );
		}

		// but always exclude function-mocker and Patchwork themselves
		$defaultExcluded      = [ $destinationFolder, Utils::getVendorDir( 'antecedent/patchwork' ) ];
		$defaultIncluded      = [ $destinationFolder . '/src/utils.php' ];
		$options['blacklist'] = ! empty( $options['blacklist'] )
			? array_merge( (array) $options['blacklist'], $defaultExcluded )
			: $defaultExcluded;

		$options['whitelist'] = ! empty( $options['whitelist'] )
			? array_merge( (array) $options['whitelist'], $defaultIncluded )
			: $defaultIncluded;

		if ( empty( $options['cache-path'] ) ) {
			$options['cache-path'] = $destinationFolder . DIRECTORY_SEPARATOR . 'cache';
		}

		return $options;
	}

	/**
	 * Undoes Patchwork bindings, use in tearDown method of test case.
	 *
	 * @return void
	 */
	public static function tearDown() {
		\Patchwork\restoreAll();
	}

	public static function __callStatic( string $function, array $arguments ): MethodProphecy {
		$instance = self::instance();

		if ( ! function_exists( $function ) ) {
			\tad\FunctionMocker\createFunction( $function );
		}

		$class                             = $instance->createClassForFunction( $function );
		$prophecy                          = $instance->prophet->prophesize( $class );
		$instance->prophecies[ $function ] = $prophecy;

		if ( empty( $arguments ) ) {
			$methodProphecy = call_user_func( [ $prophecy, $function ] );
		} else {
			$methodProphecy = call_user_func( [ $prophecy, $function ], ...$arguments );
		}

		\Patchwork\redefine( $function, function () use ( $function ) {
			$prophecy = FunctionMocker::instance()->getRevealedProphecyFor( $function );

			$args = func_get_args();

			if ( empty( $args ) ) {
				return call_user_func( [ $prophecy, $function ] );
			}

			return call_user_func( [ $prophecy, $function ], ...$args );
		} );

		return $methodProphecy;
	}

	/**
	 * @param string $function
	 */
	protected function createClassForFunction( string $function ): string {
		$uniqid    = md5( uniqid( 'function-', true ) );
		$className = "_{$function}_{$uniqid}";

		eval( "class {$className}{ public function {$function}(){}}" );

		return $className;
	}

	protected function getRevealedProphecyFor( string $function ) {
		if ( ! array_key_exists( $function, $this->revealed ) ) {
			$this->revealed[ $function ] = $this->prophecies[ $function ]->reveal();
		}

		return $this->revealed[ $function ];
	}

	/**
	 * Replaces a function, a static method or an instance method.
	 *
	 * The function or methods to be replaced must be specified with fully
	 * qualified names like
	 *
	 *     FunctionMocker::replace('my\name\space\aFunction');
	 *     FunctionMocker::replace('my\name\space\SomeClass::someMethod');
	 *
	 * not specifying a return value will make the replaced function or value
	 * return `null`.
	 *
	 * @param      $functionName
	 * @param null $returnValue
	 *
	 * @return mixed
	 */
	public static function replace( $functionName, $returnValue = null ) {
		// @todo
	}
}
