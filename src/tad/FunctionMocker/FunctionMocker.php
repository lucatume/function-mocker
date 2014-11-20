<?php

	namespace tad\FunctionMocker;

	use tad\FunctionMocker\Call\Logger\Factory as CallLoggerFactory;
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
		 * @param      $functionName
		 * @param null $returnValue
		 * @param bool $shouldReturnObject
		 * @param bool $shouldPass
		 * @param bool $spying
		 * @param bool $mocking
		 *
		 * @return null|\PHPUnit_Framework_MockObject_MockObject|Call\Logger\MockCallLogger|Call\Logger\SpyCallLogger|Call\Logger\StubCallLogger|Call\Verifier\InstanceMethodCallVerifier|static
		 */
		public static function replace( $functionName, $returnValue = null, $shouldReturnObject = true, $shouldPass = false, $spying = false, $mocking = false ) {
			\Arg::_( $functionName, 'Function name' )->is_string();

			$request     = ReplacementRequest::on( $functionName );
			$checker     = Checker::fromName( $functionName );
			$returnValue = ReturnValue::from( $returnValue );

			$callLogger = CallLoggerFactory::make( $spying, $mocking, $functionName );
			$verifier   = CallVerifierFactory::make( $request, $checker, $returnValue, $callLogger );

			$matcherInvocation = null;

			if ( $request->isInstanceMethod() ) {
				$testCase     = self::getTestCase();
				$methods      = ( $shouldPass && ! $spying ) ? array( '__construct' ) : array(
					'__construct',
					$request->getMethodName()
				);
				$mockObject = $testCase->getMockBuilder( $request->getClassName() )->disableOriginalConstructor()
				                         ->setMethods( $methods )->getMock();
				$times        = 'any';

				$matcherInvocation = $testCase->$times();
				$methodName        = $request->getMethodName();

				if ( $returnValue->isCallable() ) {
					$mockObject->expects( $matcherInvocation )->method( $methodName )
					             ->willReturnCallback( $returnValue->getValue() );
				} else {
					$value = $shouldPass ? $mockObject->$methodName() : $returnValue->getValue();

					$mockObject->expects( $matcherInvocation )->method( $methodName )->willReturn( $value );
				}
				//todo: wrap PHPUnit mock object and return it
//				$mockObject->__phpunit_setOriginalObject($mockObject);
//				if ( $spying || $mocking ) {
//					return $spying ? InstanceSpy::from( $matcherInvocation, $mockObject ) : InstanceMock::from ($matcherInvocation, $mockObject);
//				}

				return $mockObject;
			}

			// function or static method
			$functionOrMethodName = $request->isMethod() ? $request->getMethodName() : $functionName;
			// if spying do not pass
			$shouldPass = $spying ? false : $shouldPass;


			$replacementFunction = self::getReplacementFunction( $functionOrMethodName, $returnValue, $callLogger, $shouldPass );

			if ( function_exists( '\Patchwork\replace' ) ) {
				\Patchwork\replace( $functionName, $replacementFunction );
			}

			$returnObject = $mocking ? $callLogger : $verifier;

			return $shouldReturnObject ? $returnObject : null;
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
		protected static function getReplacementFunction( $functionName, $returnValue, $invocation, $shouldPass = false ) {
			$replacementFunction = function () use ( $functionName, $returnValue, $invocation, $shouldPass ) {
				$trace = debug_backtrace();
				$args  = array_filter( $trace, function ( $stackLog ) use ( $functionName ) {
					$check = isset( $stackLog['args'] ) && is_array( $stackLog['args'] ) && $stackLog['function'] === $functionName;

					return $check ? true : false;
				} );
				$args  = array_values( $args );
				$args  = isset( $args[0] ) ? $args[0]['args'] : array();
				$invocation->called( $args );

				if ( $shouldPass ) {
					\Patchwork\pass();
				} else {
					return $returnValue->isCallable() ? $returnValue->call( $args ) : $returnValue->getValue();
				}
			};

			return $replacementFunction;
		}

		public static function stub( $functionName, $returnValue = null ) {
			// applies to functions and static methods only
			$shouldReturnObject = false;
			$shouldPass         = false;
			$spying             = false;

			return self::replace( $functionName, $returnValue, $shouldReturnObject, $shouldPass, $spying );
		}

		public static function spy( $functionName, $returnValue = null ) {
			$shouldReturnObject = true;
			$shouldPass         = is_null( $returnValue );
			$spying             = true;

			return self::replace( $functionName, $returnValue, $shouldReturnObject, $shouldPass, $spying );
		}

		public static function mock( $functionName, $returnValue = null ) {
			$shouldReturnObject = true;
			$shouldPass         = is_null( $returnValue );
			$spying             = false;
			$mocking            = true;

			return self::replace( $functionName, $returnValue, $shouldReturnObject, $shouldPass, $spying, $mocking );
		}

		public static function verify() {
			MockCallLogger::verifyExpectations();
		}
	}
