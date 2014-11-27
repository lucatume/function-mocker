<?php

	namespace tad\FunctionMocker;

	use PHPUnit_Framework_MockObject_Matcher_InvokedRecorder;
	use tad\FunctionMocker\Call\Logger\CallLoggerFactory;
	use tad\FunctionMocker\Call\Verifier\CallVerifierFactory;

	class FunctionMocker {

		/**
		 * @var \PHPUnit_Framework_TestCase
		 */
		protected static $testCase;

		/**
		 * @var array
		 */
		protected static $replacedClassInstances = array();

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
					/** @noinspection PhpIncludeInspection */
					require_once $patchworkFile;
					break;
				} else {
					$dir = dirname( $dir );
				}
			}
			self::$replacedClassInstances = array();
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

			$request = ReplacementRequest::on( $functionName );
			$checker = Checker::fromName( $functionName );
			$returnValue = ReturnValue::from( $returnValue );

			$callLogger = CallLoggerFactory::make( $functionName );
			$verifier = CallVerifierFactory::make( $request, $checker, $returnValue, $callLogger );

			$invokedRecorder = null;

			$methodName = $request->getMethodName();
			if ( $request->isInstanceMethod() ) {
				$testCase = self::getTestCase();
				$className = $request->getClassName();

				if ( ! array_key_exists( $className, self::$replacedClassInstances ) ) {
					self::$replacedClassInstances[ $className ] = array();
					self::$replacedClassInstances[ $className ]['replacedMethods'] = array();
				}
				self::$replacedClassInstances[ $className ]['replacedMethods'][ $methodName ] = $returnValue;

				$classReplacedMethods = self::$replacedClassInstances[ $className ]['replacedMethods'];
				$methods = array_map( function ( $methodName ) {
					return $methodName;
				}, array_keys( $classReplacedMethods ) );
				$methods[] = '__construct';

				$mockObject = $testCase->getMockBuilder( $className )->disableOriginalConstructor()
				                       ->setMethods( $methods )->getMock();
				$times = 'any';

				/**
				 * @var PHPUnit_Framework_MockObject_Matcher_InvokedRecorder
				 */
				$invokedRecorder = $testCase->$times();

				array_walk( $classReplacedMethods, function ( ReturnValue $returnValue, $methodName, &$mockObject ) use ( $invokedRecorder ) {
					if ( $returnValue->isCallable() ) {
						$mockObject->expects( $invokedRecorder )->method( $methodName )
						           ->willReturnCallback( $returnValue->getValue() );
					} else {
						$mockObject->expects( $invokedRecorder )->method( $methodName )
						           ->willReturn( $returnValue->getValue() );
					}
				}, $mockObject );

				$wrapperInstance = null;
				if ( empty( self::$replacedClassInstances[ $className ]['instance'] ) ) {
					$mockWrapper = new MockWrapper();
					$mockWrapper->setOriginalClassName( $className );
					$wrapperInstance = $mockWrapper->wrap( $mockObject, $invokedRecorder, $request );
					self::$replacedClassInstances[ $className ]['instance'] = $wrapperInstance;
				} else {
					$wrapperInstance = self::$replacedClassInstances[ $className ]['instance'];
					/** @noinspection PhpUndefinedMethodInspection */
					$prevInvokedRecorder = $wrapperInstance->__get_functionMocker_invokedRecorder();
					// set the new invokedRecorder on the wrapper instance
					/** @noinspection PhpUndefinedMethodInspection */
					$wrapperInstance->__set_functionMocker_invokedRecorder( $invokedRecorder );
					// set the new invoked recorder on the callHandler
					$callHandler = $wrapperInstance->__get_functionMocker_CallHandler();
					$callHandler->setInvokedRecorder( $invokedRecorder );
					// sync the prev and the actual invokedRecorder
					$invocations = $prevInvokedRecorder->getInvocations();
					array_map( function ( \PHPUnit_Framework_MockObject_Invocation $invocation ) use ( &$invokedRecorder ) {
						$invokedRecorder->invoked( $invocation );
					}, $invocations );
					// set the mock object to the new one
					$wrapperInstance->__set_functionMocker_originalMockObject( $mockObject );
				}

				return $wrapperInstance;
			}

			// function or static method
			$functionOrMethodName = $request->isMethod() ? $methodName : $functionName;

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
				$args = array_filter( $trace, function ( $stackLog ) use ( $functionName ) {
					$check = isset( $stackLog['args'] ) && is_array( $stackLog['args'] ) && $stackLog['function'] === $functionName;

					return $check ? true : false;
				} );
				$args = array_values( $args );
				$args = isset( $args[0] ) ? $args[0]['args'] : array();
				/** @noinspection PhpUndefinedMethodInspection */
				$invocation->called( $args );

				/** @noinspection PhpUndefinedMethodInspection */

				/** @noinspection PhpUndefinedMethodInspection */

				/** @noinspection PhpUndefinedMethodInspection */

				return $returnValue->isCallable() ? $returnValue->call( $args ) : $returnValue->getValue();
			};

			return $replacementFunction;
		}
	}
