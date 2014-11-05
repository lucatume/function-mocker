<?php

	namespace tad\FunctionMocker;

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
		public static function load() {
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
		public static function unload() {
			\Patchwork\undoAll();
		}

		/**
		 * Mocks a function, a static method or an instance method.
		 *
		 * To mock functions and static methods Patchwork will be used
		 * hence `load` and `unload` methods are required.
		 * When mocking instance methods a PHPUnit mock object will be
		 * returned and the expectation on it will be set using the
		 * `PHPUnit_Framework_TestCase::any` method.
		 *
		 * @param string              $functionName The name of the function to mock.
		 *                                          Name spaced or not; to mock a class
		 *                                          method use the `Class::method`
		 *                                          notation.
		 * @param null|mixed|callable $returnValue  The return value.
		 *                                          Either null, a value or a callable
		 *                                          that will be returned when the
		 *                                          function or method is invoked.
		 * @param                     int           times            The number of times the method is expected
		 *                                          to be called on the instance (applies to
		 *                                          mocked instance methods only)
		 *
		 * @return PHPUnit_Framework_MockObject_MockObject|\tad\FunctionMocker\FunctionMatcher
		 */
		public static function mock( $functionName, $returnValue = null, $shouldReturnObject = true, $shouldPass = false, $spying = false, $times = null ) {
			\Arg::_( $functionName, 'Function name' )->is_string();

			$request = MockRequestParser::on( $functionName );
			$checker = Checker::fromName( $functionName );
			$returnValue = ReturnValue::from( $returnValue );
			$invocation = new Invocation();
			$matcher = FunctionMatcher::__from( $checker, $returnValue, $invocation );
			$matcherInvocation = null;

			if ( $request->isInstanceMethod() ) {
				$testCase = self::getTestCase();
				$methods = $shouldPass ? array( '__construct' ) : array( '__construct', $request->getMethodName() );
				$mockInstance = $testCase->getMockBuilder( $request->getClassName() )->disableOriginalConstructor()
				                         ->setMethods( $methods )->getMock();
				$timeIntValue = null;
				if ( ! is_null( $times ) ) {
					$timeIntValue = (int) $times;
					switch ( $times ) {
						case 0:
							$times = 'never';
							break;
						case 1:
							$times = 'once';
							break;
						default:
							$times = 'exactly';
							break;
					}
				} else {
					$times = 'any';
				}

				if ( $returnValue->isCallable() ) {

					$mockInstance->expects( $matcherInvocation = $testCase->$times( $timeIntValue ) )
					             ->method( $request->getMethodName() )->willReturnCallback( $returnValue->getValue() );
				} else {
					$value = $shouldPass ? $mockInstance->{$request->getMethodName()}() : $returnValue->getValue();

					$mockInstance->expects( $matcherInvocation = $testCase->$times( $timeIntValue ) )
					             ->method( $request->getMethodName() )->willReturn( $value );
				}

				return $spying ? InstanceSpy::from( $matcherInvocation, $mockInstance ) : $mockInstance;
			}

			// function or static method
			$replacementFunction = self::getReplacementFunction( $functionName, $returnValue, $invocation, $shouldPass );

			if ( function_exists( '\Patchwork\replace' ) ) {
				\Patchwork\replace( $functionName, $replacementFunction );
			}

			return $shouldReturnObject ? $matcher : null;
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
				$args = array_filter( $trace, function ( $stackLog ) use ( $functionName ) {
					$check = isset( $stackLog['args'] ) && is_array( $stackLog['args'] ) && $stackLog['function'] === $functionName;

					return $check ? true : false;
				} );
				$args = array_values( $args );
				$args = isset( $args[0] ) ? $args[0]['args'] : array();
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
			$shouldPass = false;
			$spying = false;

			return self::mock( $functionName, $returnValue, $shouldReturnObject, $shouldPass, $spying );
		}

		public static function spy( $functionName, $returnValue = null ) {
			$shouldReturnObject = true;
			$shouldPass = is_null( $returnValue );
			$spying = true;

			return self::mock( $functionName, $returnValue, $shouldReturnObject, $shouldPass, $spying );
		}
	}
