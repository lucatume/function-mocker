<?php

namespace tad\FunctionMocker;

use Prophecy\Prophecy\MethodProphecy;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophecy\ProphecyInterface;
use Prophecy\Prophet;

/**
 * Class FunctionMocker
 *
 * @package tad\FunctionMocker
 */
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
	protected $prophecies = [];

	/**
	 * @var MethodProphecy[]
	 */
	protected $methodProphecies = [];

	/**
	 * @var \Prophecy\Prophet
	 */
	protected $prophet;

	protected function __construct( Prophet $prophet ) {
		$this->prophet = $prophet;
	}

	/**
	 * Sets up the class to redefine, stub and mock functions, use in the `setUp` method of the test case and in conjunction
	 * with the `tad\FunctionMocker\FunctionMocker\tearDown` method.
	 *
	 * Example usage:
	 *
	 *      use \tad\FunctionMocker\FunctionMocker;
	 *
	 *      class MyTestCase extends \PHPUnit\Framework\TestCase {
	 *          public function setUp(){
	 *              FunctionMocker::setUp();
	 *          }
	 *
	 *          public function test_something_requiring_function_mocking(){
	 *              // ...
	 *          }
	 *
	 *          public function tearDown(){
	 *              FunctionMocker::tearDown();
	 *          }
	 *      }
	 *
	 * @see \tad\FunctionMocker\FunctionMocker::tearDown()
	 */
	public static function setUp() {
		if ( ! self::instance()->didInit ) {
			self::init();
		}
		static::instance()->prophet = new Prophet();
	}

	protected static function instance(): FunctionMocker {
		if ( static::$instance === null ) {
			static::$instance = new static( new Prophet() );
		}

		return static::$instance;
	}

	/**
	 * Inits the mocking engine including the Patchwork library and configuring it.
	 *
	 * @param array|null $options An array of options to init the Patchwork library.
	 *                            ['include'|'whitelist']     array|string A list of absolute paths that should be included in the patching.
	 *                            ['exclude'|'blacklist']     array|string A list of absolute paths that should be excluded in the patching.
	 *                            ['cache-path']              string The absolute path to the folder where Patchwork should cache the wrapped files.
	 *                            ['redefinable-internals']   array A list of internal PHP functions that are available for replacement.
	 *
	 * @see \Patchwork\configure()
	 */
	public static function init( array $options = null ) {
		$function_mocker = static::instance();

		if ( $function_mocker->didInit ) {
			return;
		}

		writePatchworkConfig( $options );

		/** @noinspection PhpIncludeInspection */
		includePatchwork();

		require_once __DIR__ . '/utils.php';

		$function_mocker->didInit = true;
	}

	/**
	 * Tears down the class artifacts after a test, use in the `tearDown` method of the test case and in conjunction
	 * with the `tad\FunctionMocker\FunctionMocker\setUp` method.
	 *
	 * Example usage:
	 *
	 *      use \tad\FunctionMocker\FunctionMocker;
	 *
	 *      class MyTestCase extends \PHPUnit\Framework\TestCase {
	 *          public function setUp(){
	 *              FunctionMocker::setUp();
	 *          }
	 *
	 *          public function test_something_requiring_function_mocking(){
	 *              // ...
	 *          }
	 *
	 *          public function tearDown(){
	 *              FunctionMocker::tearDown();
	 *          }
	 *      }
	 *
	 * @see \tad\FunctionMocker\FunctionMocker::setUp()
	 */
	public static function tearDown() {
		\Patchwork\restoreAll();

		if ( self::instance()->prophet === null ) {
			return;
		}

		$prophet                    = static::instance()->prophet;
		static::instance()->prophet = null;
		$prophet->checkPredictions();
	}

	/**
	 * Magic method to offer a flexible function-mocking API.
	 *
	 * Example usage:
	 *
	 *      use tad\FunctionMocker\FunctionMocker;
	 *
	 *      class MyTestCase extends \PHPUnit\Framework\TestCase {
	 *          public function setUp(){
	 *              FunctionMocker::setUp();
	 *          }
	 *
	 *          public function test_something_requiring_function_mocking(){
	 *              FunctionMocker::update_option('foo', ['bar'])
	 *                  ->shouldBeCalled();
	 *              FunctionMocker::get_option(Argument::type('string'))
	 *                  ->willReturn([]);
	 *
	 *              $logger = new OptionLogger($option = 'foo');
	 *              $logger->log('bar');
	 *          }
	 *
	 *          public function tearDown(){
	 *              FunctionMocker::tearDown();
	 *          }
	 *      }
	 *
	 * @param string $function  Handled by PHP; if calling `FunctionMocker::update_option` then this
	 *                          will be `update_option`; see usage example above.
	 * @param array  $arguments Handled by PHP
	 *
	 * @return \Prophecy\Prophecy\MethodProphecy
	 */
	public static function __callStatic( string $function, array $arguments ): MethodProphecy {
		return self::replace( $function, ...$arguments );
	}

	/**
	 * Replaces a function, be it defined or not, with Patchwork.
	 *
	 * This method is the one used by the `__callStatic` implementation and is the
	 * one that should be used to replace namespaced functions.
	 *
	 * Example usage:
	 *
	 *      use tad\FunctionMocker\FunctionMocker;
	 *
	 *      class MyTestCase extends \PHPUnit\Framework\TestCase {
	 *          public function setUp(){
	 *              FunctionMocker::setUp();
	 *          }
	 *
	 *          public function test_something_requiring_function_mocking(){
	 *              FunctionMocker::replace('MyNamespace\\SubSpace\\update_option', 'foo', ['bar'])
	 *                  ->shouldBeCalled();
	 *              FunctionMocker::replace('MyNamespace\\SubSpace\\get_option', Argument::type('string'))
	 *                  ->willReturn([]);
	 *
	 *              $logger = new OptionLogger($option = 'foo');
	 *              $logger->log('bar');
	 *          }
	 *
	 *          public function tearDown(){
	 *              FunctionMocker::tearDown();
	 *          }
	 *      }
	 *
	 * @param string $function     The name of the function to replace, including the namespace.
	 * @param mixed  ...$arguments Arguments that are expected to be used to call the function.
	 *                             The arguments can be scalar or instances of the `Prophecy\Argument`
	 *                             class.
	 *
	 * @return \Prophecy\Prophecy\MethodProphecy
	 * @throws \Exception
	 */
	public static function replace( string $function, ...$arguments ): MethodProphecy {
		$instance = self::instance();

		list( $function, $namespace, $functionFQN ) = $instance->extractFunctionAndNamespace( $function );

		if ( ! function_exists( $functionFQN ) ) {
			\tad\FunctionMocker\createFunction( $function, $namespace );
		}

		$prophecy = $instance->buildProphecyFor( $function, $functionFQN );
		$instance->redefineFunctionWithPatchwork( $function, $functionFQN );

		return $instance->buildMethodProphecy( $function, $functionFQN, $arguments, $prophecy );
	}

	protected function extractFunctionAndNamespace( string $function ): array {
		$function       = '\\' . ltrim( $function, '\\' );
		$namespaceFrags = array_filter( explode( '\\', $function ) );
		$function       = array_pop( $namespaceFrags );
		$namespace      = implode( '\\', $namespaceFrags );
		$functionFQN    = $namespace . '\\' . $function;

		return array( $function, $namespace, $functionFQN );
	}

	protected function buildProphecyFor( string $function, $functionFQN ) {
		if ( ! array_key_exists( $functionFQN, $this->prophecies ) ) {
			$class                            = $this->createClassForFunction( $function, $functionFQN );
			$prophecy                         = $this->prophet->prophesize( $class );
			$this->prophecies[ $functionFQN ] = $prophecy;
		} else {
			$prophecy = $this->prophecies[ $functionFQN ];
		}

		return $prophecy;
	}

	protected function createClassForFunction( string $function, string $functionFQN ): string {
		$uniqid       = md5( uniqid( 'function-', true ) );
		$functionSlug = str_replace( '\\', '_', $functionFQN );
		$className    = "_{$functionSlug}_{$uniqid}";

		eval( "class {$className}{ public function {$function}(){}}" );

		return $className;
	}

	protected function redefineFunctionWithPatchwork( string $function, string $functionFQN ) {
		\Patchwork\redefine( $functionFQN, function () use ( $functionFQN, $function ) {
			$prophecy = FunctionMocker::instance()->getRevealedProphecyFor( $functionFQN );
			$args     = func_get_args();

			if ( empty( $args ) ) {
				return call_user_func( [ $prophecy, $function ] );
			}

			return call_user_func( [ $prophecy, $function ], ...$args );
		} );
	}

	protected function getRevealedProphecyFor( string $function ) {
		if ( ! array_key_exists( $function, $this->revealed ) ) {
			$this->revealed[ $function ] = $this->prophecies[ $function ]->reveal();
		}

		return $this->revealed[ $function ];
	}

	protected function buildMethodProphecy( string $function, string $functionFQN, array $arguments, ObjectProphecy $prophecy ) {
		if ( ! array_key_exists( $functionFQN, $this->methodProphecies ) ) {
			if ( empty( $arguments ) ) {
				$methodProphecy = call_user_func( [ $prophecy, $function ] );
			} else {
				$methodProphecy = call_user_func( [ $prophecy, $function ], ...$arguments );
			}
			$this->methodProphecies[ $functionFQN ] = $methodProphecy;
		}

		return $this->methodProphecies[ $functionFQN ];
	}
}
