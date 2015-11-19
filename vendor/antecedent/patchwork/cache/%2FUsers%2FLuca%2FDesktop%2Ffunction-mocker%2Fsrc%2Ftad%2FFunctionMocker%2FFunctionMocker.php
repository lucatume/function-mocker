<?php

namespace tad\FunctionMocker; \Patchwork\Interceptor\deployQueue();

use tad\FunctionMocker\Call\Logger\CallLoggerFactory;
use tad\FunctionMocker\Call\Verifier\CallVerifierFactory;
use tad\FunctionMocker\Call\Verifier\FunctionCallVerifier;
use tad\FunctionMocker\Forge\Step;
use tad\FunctionMocker\Replacers\InstanceForger;

class FunctionMocker {
	// allows wrapping assert methods
	use PHPUnitFrameworkAssertWrapper;

	/**
	 * @var \PHPUnit_Framework_TestCase
	 */
	protected static $testCase;

	/** @var  array */
	protected static $defaultWhitelist = array(
		'vendor/antecedent'
	);

	protected static $defaultBlacklist = array(
		'vendor/codeception',
		'vendor/phpunit',
		'vendor/phpspec'
	);
	/**
	 * Stores the previous values of each global replaced.
	 *
	 * @var array
	 */
	protected static $globalsBackup = [ ];
	/** @var  bool */
	private static $didInit = false;

	/**
	 * Loads Patchwork, use in setUp method of the test case.
	 *
	 * @return void
	 */
	public static function setUp() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		if ( ! self::$didInit ) {
			self::init();
		}
	}

	public static function init( array $options = null ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		if ( self::$didInit ) {
			return;
		}

		/** @noinspection PhpIncludeInspection */
		require_once Utils::getPatchworkFilePath();

		$_whitelist = is_array( $options['include'] ) ? array_merge( self::$defaultWhitelist, $options['include'] ) : self::$defaultWhitelist;
		$_blacklist = is_array( $options['exclude'] ) ? array_merge( self::$defaultBlacklist, $options['exclude'] ) : self::$defaultBlacklist;

		$rootDir   = Utils::findParentContainingFrom( 'vendor', dirname( __FILE__ ) );
		$whitelist = Utils::filterPathListFrom( $_whitelist, $rootDir );
		$blacklist = Utils::filterPathListFrom( $_blacklist, $rootDir );

		$blacklist = array_diff( $blacklist, $whitelist );

		array_map( function ( $path ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			\Patchwork\blacklist( $path );
		}, $blacklist );

		self::$didInit = true;
	}

	/**
	 * Undoes Patchwork bindings, use in tearDown method of test case.
	 *
	 * @return void
	 */
	public static function tearDown() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		\Patchwork\undoAll();

		// restore the globals
		if ( empty( self::$globalsBackup ) ) {
			return;
		}
		array_walk( self::$globalsBackup, function ( $value, $key ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			$GLOBALS[ $key ] = $value;
		} );
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
	public static function replace( $functionName, $returnValue = null ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		\Arg::_( $functionName, 'Function name' )->is_string()->_or()->is_array();
		if ( is_array( $functionName ) ) {
			$replacements = array();
			array_map( function ( $_functionName ) use ( $returnValue, &$replacements ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
				$replacements[] = self::_replace( $_functionName, $returnValue );
			}, $functionName );

			$indexedReplacements = self::getIndexedReplacements( $replacements );

			return $indexedReplacements;
		}

		return self::_replace( $functionName, $returnValue );
	}

	/**
	 * @param $functionName
	 * @param $returnValue
	 *
	 * @return mixed|null|Call\Verifier\InstanceMethodCallVerifier|static
	 * @throws \Exception
	 */
	private static function _replace( $functionName, $returnValue ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		$request     = ReplacementRequest::on( $functionName );
		$returnValue = ReturnValue::from( $returnValue );
		$methodName  = $request->getMethodName();

		if ( $request->isClass() ) {
			return self::get_instance_replacement_chain_head( $functionName );
		}
		if ( $request->isInstanceMethod() ) {
			return self::get_instance_replacement( $request, $returnValue );
		}

		return self::get_function_or_static_method_replacement( $functionName, $returnValue, $request, $methodName );
	}

	/**
	 * @param ReplacementRequest $request
	 * @param $returnValue
	 *
	 * @return mixed
	 */
	public static function get_instance_replacement( ReplacementRequest $request, $returnValue ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		$forger = new InstanceForger();
		$forger->setTestCase( self::getTestCase() );

		return $forger->getMock( $request, $returnValue );
	}

	/**
	 * @return SpoofTestCase
	 */
	protected static function getTestCase() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		if ( ! self::$testCase ) {
			self::$testCase = new SpoofTestCase();
		}
		$testCase = self::$testCase;

		return $testCase;
	}

	/**
	 * @param \PHPUnit_Framework_TestCase $testCase
	 */
	public static function setTestCase( $testCase ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		self::$testCase = $testCase;
	}


	/**
	 * @param $functionName
	 * @param $returnValue
	 * @param $request
	 * @param $methodName
	 *
	 * @return Call\Verifier\InstanceMethodCallVerifier|static
	 * @throws \Exception
	 */
	private static function get_function_or_static_method_replacement( $functionName, $returnValue, $request, $methodName ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		$checker    = Checker::fromName( $functionName );
		$callLogger = CallLoggerFactory::make( $functionName );
		$verifier   = CallVerifierFactory::make( $request, $checker, $returnValue, $callLogger );
		self::replace_with_patchwork( $functionName, $returnValue, $request, $methodName, $callLogger );

		return $verifier;
	}

	/**
	 * @param $functionName
	 * @param $returnValue
	 * @param $request
	 * @param $methodName
	 * @param $callLogger
	 */
	private static function replace_with_patchwork( $functionName, $returnValue, $request, $methodName, $callLogger ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		$functionOrMethodName = $request->isMethod() ? $methodName : $functionName;

		$replacementFunction = self::getReplacementFunction( $functionOrMethodName, $returnValue, $callLogger );

		if ( function_exists( '\Patchwork\replace' ) ) {

			\Patchwork\replace( $functionName, $replacementFunction );
		}
	}

	/**
	 * @param $functionName
	 * @param $returnValue
	 * @param $invocation
	 *
	 * @return callable
	 */
	protected static function getReplacementFunction( $functionName, $returnValue, $invocation ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		$replacementFunction = function () use ( $functionName, $returnValue, $invocation ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			$trace = debug_backtrace();
			$args  = array_filter( $trace, function ( $stackLog ) use ( $functionName ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
				$check = isset( $stackLog['args'] ) && is_array( $stackLog['args'] ) && $stackLog['function'] === $functionName;

				return $check ? true : false;
			} );
			$args  = array_values( $args );
			$args  = isset( $args[0] ) ? $args[0]['args'] : array();
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
	 * @param $elements
	 *
	 * @return array|mixed
	 */
	private static function arrayUnique( $elements ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		$uniqueReplacements = array();
		array_map( function ( $replacement ) use ( &$uniqueReplacements ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
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
	private static function getIndexedReplacements( $return ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		$indexedReplacements = array();
		if ( $return[0] instanceof FunctionCallVerifier ) {
			array_map( function ( FunctionCallVerifier $replacement ) use ( &$indexedReplacements ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
				$fullFunctionName                     = $replacement->__getFunctionName();
				$functionNameElements                 = preg_split( '/(\\\\|::)/', $fullFunctionName );
				$functionName                         = array_pop( $functionNameElements );
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
	public static function callOriginal( array $args = null ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		return \Patchwork\callOriginal( $args );
	}

	/**
	 * Replaces/sets a global object with an instance replacement of the class.
	 *
	 * The $GLOBALS state will be reset at the next `FunctionMocker::tearDown` call.
	 *
	 * @param  string $globalHandle The key the value is associated to in the $GLOBALS array.
	 * @param  string $functionName A `Class::method` format string
	 * @param  mixed $returnValue The return value or callback, see `replace` method.
	 *
	 * @return mixed               The object that's been set in the $GLOBALS array.
	 */
	public static function replaceGlobal( $globalHandle, $functionName, $returnValue = null ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		\Arg::_( $globalHandle, 'Global var key' )->is_string();

		self::backupGlobal( $globalHandle );

		$replacement              = FunctionMocker::_replace( $functionName, $returnValue );
		$GLOBALS[ $globalHandle ] = $replacement;

		return $replacement;
	}

	protected static function backupGlobal( $globalHandle ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		$shouldSave = ! isset( self::$globalsBackup[ $globalHandle ] );
		if ( ! $shouldSave ) {
			return;
		}
		self::$globalsBackup[ $globalHandle ] = isset( $GLOBALS[ $globalHandle ] ) ? $GLOBALS[ $globalHandle ] : null;
	}

	/**
	 * Sets a global value restoring the state after the test ran.
	 *
	 * @param string $globalHandle The key the value will be associated to in the $GLOBALS array.
	 * @param mixed $replacement The value that will be set in the $GLOBALS array.
	 *
	 * @return mixed               The object that's been set in the $GLOBALS array.
	 */
	public static function setGlobal( $globalHandle, $replacement = null ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		\Arg::_( $globalHandle, 'Global var key' )->is_string();

		self::backupGlobal( $globalHandle );

		$GLOBALS[ $globalHandle ] = $replacement;

		return $replacement;
	}

	public static function forge( $class ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		return new Step( $class );
	}

	private static function get_instance_replacement_chain_head( $className ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		$step = new Step();
		$step->setClass( $className );
		$forger = new InstanceForger();
		$forger->setTestCase( self::getTestCase() );
		$step->setInstanceForger( $forger );

		return $step;
	}
}\Patchwork\Interceptor\deployQueue();
