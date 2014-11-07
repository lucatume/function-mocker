<?php

	namespace tad\FunctionMocker;

	use src\tad\FunctionMocker\StubInvocation;
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

		public static function replace( $functionName, $returnValue = null, $shouldReturnObject = true, $shouldPass = false, $spying = false, $mocking = false ) {
			\Arg::_( $functionName, 'Function name' )->is_string();

			$request     = MockRequestParser::on( $functionName );
			$checker     = Checker::fromName( $functionName );
			$returnValue = ReturnValue::from( $returnValue );

			$invocation = CallLoggerFactory::make( $spying, $mocking );

			$matcher           = FunctionCallVerifier::__from( $checker, $returnValue, $invocation );
			$matcherInvocation = null;

			if ( $request->isInstanceMethod() ) {
				$testCase     = self::getTestCase();
				$methods      = ( $shouldPass && ! $spying ) ? array( '__construct' ) : array(
					'__construct',
					$request->getMethodName()
				);
				$mockInstance = $testCase->getMockBuilder( $request->getClassName() )->disableOriginalConstructor()
				                         ->setMethods( $methods )->getMock();
				$times        = 'any';

				$matcherInvocation = $testCase->$times();
				$methodName        = $request->getMethodName();

				if ( $returnValue->isCallable() ) {
					$mockInstance->expects( $matcherInvocation )->method( $methodName )
					             ->willReturnCallback( $returnValue->getValue() );
				} else {
					$value = $shouldPass ? $mockInstance->$methodName() : $returnValue->getValue();

					$mockInstance->expects( $matcherInvocation )->method( $methodName )->willReturn( $value );
				}
				if ( $spying || $mocking ) {
					// todo: return InstanceMock here when mockin
					return $spying ? InstanceSpy::from( $matcherInvocation, $mockInstance ) : false;
				}

				return $mockInstance;
			}

			// function or static method
			$functionOrMethodName = $request->isMethod() ? $request->getMethodName() : $functionName;
			// if spying do not pass
			$shouldPass = $spying ? false : $shouldPass;


			$replacementFunction = self::getReplacementFunction( $functionOrMethodName, $returnValue, $invocation, $shouldPass );

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
	}
