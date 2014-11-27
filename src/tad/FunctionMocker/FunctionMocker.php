<?php

	namespace tad\FunctionMocker;

	use tad\FunctionMocker\Call\Logger\CallLoggerFactory as CallLoggerFactory;
	use tad\FunctionMocker\Call\Logger\MockCallLogger;
	use tad\FunctionMocker\Call\Verifier\Factory as CallVerifierFactory;
	use tad\FunctionMocker\SpoofTestCase;

	class FunctionMocker {

		/**
		 * @var \PHPUnit_Framework_TestCase
		 */
		protected static $testCase;

		/**
		 * Loads Patchwork, use in setUp method of the test case.
		 *
		 * @return void
		 */
		public static function setUp() {
			$dir = __DIR__;
			while ( true ) {
				if ( file_exists( $dir . '/vendor' ) ) {
					$patchworkFile = $dir . "/vendor/antecedent/patchwork/Patchwork.php";
					require_once $patchworkFile;
					break;
				} else {
					$dir = dirname( $dir );
				}
			}
		}

		/**
		 * Undoes Patchwork bindings, use in tearDown method of test case.
		 *
		 * @return void
		 */
		public static function tearDown() {
			\Patchwork\undoAll();
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
		 * @return mixed|Call\Verifier\InstanceMethodCallVerifier|static
		 */
		public static function replace( $functionName, $returnValue = null ) {
			\Arg::_( $functionName, 'Function name' )->is_string();

			$request     = ReplacementRequest::on( $functionName );
			$checker     = Checker::fromName( $functionName );
			$returnValue = ReturnValue::from( $returnValue );

			$callLogger = CallLoggerFactory::make( $functionName );
			$verifier   = CallVerifierFactory::make( $request, $checker, $returnValue, $callLogger );

			$invokedRecorder = null;

			if ( $request->isInstanceMethod() ) {
				$testCase   = self::getTestCase();
				$methods    = array( '__construct', $request->getMethodName() );
				$mockObject = $testCase->getMockBuilder( $request->getClassName() )->disableOriginalConstructor()
				                       ->setMethods( $methods )->getMock();
				$times      = 'any';

				$invokedRecorder = $testCase->$times();
				$methodName      = $request->getMethodName();

				if ( $returnValue->isCallable() ) {
					$mockObject->expects( $invokedRecorder )->method( $methodName )
					           ->willReturnCallback( $returnValue->getValue() );
				} else {
					$mockObject->expects( $invokedRecorder )->method( $methodName )
					           ->willReturn( $returnValue->getValue() );
				}
				$mockWrapper = new MockWrapper();
				$mockWrapper->setOriginalClassName( $request->getClassName() );
				$wrapperInstance = $mockWrapper->wrap( $mockObject, $invokedRecorder, $request );

				return $wrapperInstance;
			}

			// function or static method
			$functionOrMethodName = $request->isMethod() ? $request->getMethodName() : $functionName;

			$replacementFunction = self::getReplacementFunction( $functionOrMethodName, $returnValue, $callLogger );

			if ( function_exists( '\Patchwork\replace' ) ) {
				\Patchwork\replace( $functionName, $replacementFunction );
			}

			return $verifier;
		}

		/**
		 * @return SpoofTestCase
		 */
		protected static function getTestCase() {
			if ( ! self::$testCase ) {
				self::$testCase = new SpoofTestCase();
			}
			$testCase = self::$testCase;

			return $testCase;
		}

		/**
		 * @param $functionName
		 * @param $returnValue
		 * @param $invocation
		 *
		 * @return callable
		 */
		protected static function getReplacementFunction( $functionName, $returnValue, $invocation ) {
			$replacementFunction = function () use ( $functionName, $returnValue, $invocation ) {
				$trace = debug_backtrace();
				$args  = array_filter( $trace, function ( $stackLog ) use ( $functionName ) {
					$check = isset( $stackLog['args'] ) && is_array( $stackLog['args'] ) && $stackLog['function'] === $functionName;

					return $check ? true : false;
				} );
				$args  = array_values( $args );
				$args  = isset( $args[0] ) ? $args[0]['args'] : array();
				$invocation->called( $args );

				return $returnValue->isCallable() ? $returnValue->call( $args ) : $returnValue->getValue();
			};

			return $replacementFunction;
		}
	}
