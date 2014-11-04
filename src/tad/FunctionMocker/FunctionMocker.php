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
		 * @param string $functionName The name of the function to mock.
		 *                             Name spaced or not; to mock a class
		 *                             method use the `Class::method`
		 *                             notation.
		 * @param null|mixed|callable $returnValue The return value.
		 *        					   Either null, a value or a callable
		 *                             that will be returned when the
		 *                             function or method is invoked.
		 * 
		 * @return PHPUnit_Framework_MockObject_MockObject|tad\FunctionMocker\Matcher
		 */
		public static function mock( $functionName, $returnValue = null ) {
			\Arg::_( $functionName, 'Function name' )->is_string();

			$request = MockRequestParser::on( $functionName );
			$checker = Checker::fromName( $functionName );
			$returnValue = ReturnValue::from( $returnValue );
			$invocation = new Invocation();
			$matcher = Matcher::__from( $checker, $returnValue, $invocation );

			if ( $request->isInstanceMethod() ) {
				$testCase = self::getTestCase();
				$mockInstance = $testCase->getMock( $request->getClassName() );
				if ( $returnValue->isCallable() ) {

					$mockInstance->expects( $testCase->any() )->method( $request->getMethodName() )
					             ->willReturnCallback( $returnValue->getValue() );
				} else {

					$mockInstance->expects( $testCase->any() )->method( $request->getMethodName() )
					             ->willReturn( $returnValue->getValue() );
				}

				return $mockInstance;
			}

			$replacementFunction = self::getReplacementFunction( $functionName, $returnValue, $invocation );

			if ( function_exists( '\Patchwork\replace' ) ) {
				\Patchwork\replace( $functionName, $replacementFunction );
			}

			return $matcher;
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
				$args = array_filter( $trace, function ( $stackLog ) use ( $functionName ) {
					$check = isset( $stackLog['args'] ) && is_array( $stackLog['args'] ) && $stackLog['function'] === $functionName;

					return $check ? true : false;
				} );
				$args = array_values( $args );
				$args = isset( $args[0] ) ? $args[0]['args'] : array();
				$invocation->called( $args );

				return $returnValue->isCallable() ? $returnValue->call( $args ) : $returnValue->getValue();
			};

			return $replacementFunction;
		}
	}
