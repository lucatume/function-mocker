<?php

	namespace tad\FunctionMocker;

	use PHPUnit_Framework_MockObject_Matcher_InvokedRecorder;
	use src\tad\FunctionMocker\Utils;
	use tad\FunctionMocker\Call\Logger\CallLoggerFactory;
	use tad\FunctionMocker\Call\Verifier\CallVerifierFactory;
	use tad\FunctionMocker\Call\Verifier\FunctionCallVerifier;

	class FunctionMocker {

		/**
		 * @var \PHPUnit_Framework_TestCase
		 */
		protected static $testCase;

		/**
		 * @var array
		 */
		protected static $replacedClassInstances = array();

		/** @var  array */
		public static $defaultWhitelist = array(
			'vendor/antecedent'
		);

		/** @var  bool */
		private static $didInit = false;

		/**
		 * Loads Patchwork, use in setUp method of the test case.
		 *
		 * @return void
		 */
		public static function setUp() {
			if ( ! self::$didInit ) {
				self::init();
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
			\Arg::_( $functionName, 'Function name' )->is_string()->_or()->is_array();
			if ( is_array( $functionName ) ) {
				$replacements = array();
				array_map( function ( $_functionName ) use ( $returnValue, &$replacements ) {
					$replacements[] = self::_replace( $_functionName, $returnValue );
				}, $functionName );

				$return = self::arrayUnique( $replacements );
				if ( ! is_array( $return ) ) {
					return $return;
				}

				$indexedReplacements = self::getIndexedReplacements( $return );

				return $indexedReplacements;
			}

			return self::_replace( $functionName, $returnValue );
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

		/**
		 * @param $functionName
		 * @param $returnValue
		 *
		 * @return mixed|null|Call\Verifier\InstanceMethodCallVerifier|static
		 * @throws \Exception
		 */
		private static function _replace( $functionName, $returnValue ) {
			$request = ReplacementRequest::on( $functionName );
			$checker = Checker::fromName( $functionName );
			$returnValue = ReturnValue::from( $returnValue );

			$callLogger = CallLoggerFactory::make( $functionName );
			$verifier = CallVerifierFactory::make( $request, $checker, $returnValue, $callLogger );

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

				array_walk( $classReplacedMethods, function ( ReturnValue $returnValue, $methodName, \PHPUnit_Framework_MockObject_MockObject &$mockObject ) use ( $invokedRecorder ) {
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
		 * @param $elements
		 *
		 * @return array|mixed
		 */
		private static function arrayUnique( $elements ) {
			$uniqueReplacements = array();
			array_map( function ( $replacement ) use ( &$uniqueReplacements ) {
				if ( ! in_array( $replacement, $uniqueReplacements ) ) {
					$uniqueReplacements[] = $replacement;
				}
			}, $elements );
			$uniqueReplacements = array_values( $uniqueReplacements );

			return count( $uniqueReplacements ) === 1 ? $uniqueReplacements[0] : $uniqueReplacements;
		}

		/**
		 * @param $return
		 *
		 * @return array
		 */
		private static function getIndexedReplacements( $return ) {
			$indexedReplacements = array();
			if ( $return[0] instanceof FunctionCallVerifier ) {
				array_map( function ( FunctionCallVerifier $replacement ) use ( &$indexedReplacements ) {
					$fullFunctionName = $replacement->__getFunctionName();
					$functionNameElements = preg_split( '/(\\\\|::)/', $fullFunctionName );
					$functionName = array_pop( $functionNameElements );
					$indexedReplacements[ $functionName ] = $replacement;
				}, $return );

			}

			return $indexedReplacements;
		}

		/**
		 * Calls the original function or static method with the given arguments
		 * and returns the return value if any.
		 *
		 * @param array $args
		 *
		 * @return mixed
		 */
		public static function callOriginal( array $args = null ) {
			return \Patchwork\callOriginal( $args );
		}

		public static function init( array $options = null ) {
			if ( self::$didInit ) {
				return;
			}
			$rootDir = Utils::findParentContainingFrom( 'vendor', dirname( __FILE__ ) );
			$patchworkFile = $rootDir . "/vendor/antecedent/patchwork/Patchwork.php";
			/** @noinspection PhpIncludeInspection */
			require_once $patchworkFile;

			$_whitelist = is_array( $options['include'] ) ? array_merge( self::$defaultWhitelist, $options['include'] ) : self::$defaultWhitelist;
			$whitelist = array_map( function ( $frag ) use ( $rootDir ) {
				return $rootDir . DIRECTORY_SEPARATOR . Utils::normalizePathFrag( $frag );
			}, $_whitelist );

			$blacklist = glob( $rootDir . '/vendor/*', GLOB_ONLYDIR );
			$blacklist = is_array( $options['exclude'] ) ? array_merge( $blacklist, $options['exclude'] ) : $blacklist;

			$blacklist = array_diff( $blacklist, $whitelist );

			array_map( function ( $path ) {
				\Patchwork\blacklist( $path );
			}, $blacklist );

			self::$didInit = true;
		}
	}
