<?php

	namespace tad\FunctionMocker\Tests; \Patchwork\Interceptor\deployQueue();

	use tad\FunctionMocker\FunctionMocker;
	use tad\FunctionMocker\MockCallLogger;
	use tad\FunctionMocker\TestCase;

	class FunctionMockerStaticMethodTest extends \PHPUnit_Framework_TestCase {

		public function setUp() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			FunctionMocker::setUp();
		}

		public function tearDown() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			FunctionMocker::tearDown();
		}

		/**
		 * @test
		 * it should return a VerifierInterface when replacing a static method
		 */
		public function it_should_return_a_verifier_when_replacing_a_static_method() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			$ret = FunctionMocker::replace( __NAMESPACE__ . '\SomeClass::staticMethod' );

			$this->assertInstanceOf( 'tad\FunctionMocker\Call\Verifier\VerifierInterface', $ret );
		}

		/**
		 * @test
		 * it should allow setting various return values when replacing a static method
		 */
		public function it_should_allow_setting_various_return_values_when_replacing_a_static_method() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			$ret = FunctionMocker::replace( __NAMESPACE__ . '\SomeClass::staticMethod', 23 );

			$this->assertEquals( 23, SomeClass::staticMethod() );

			$ret = FunctionMocker::replace( __NAMESPACE__ . '\SomeClass::staticMethod', function ( $a ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
				return $a + 1;
			} );

			$this->assertEquals( 24, SomeClass::staticMethod( 23 ) );

			$ret = FunctionMocker::replace( __NAMESPACE__ . '\SomeClass::staticMethod', function ( $a, $b ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
				return $a + $b;
			} );

			$this->assertEquals( 24, SomeClass::staticMethod( 23, 1 ) );
		}

		/**
		 * @test
		 * it should return a matcher when spying a static method
		 */
		public function it_should_return_a_matcher_when_spying_a_static_method() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			$ret = FunctionMocker::replace( __NAMESPACE__ . '\SomeClass::staticMethod' );

			$this->assertInstanceOf( 'tad\FunctionMocker\Call\Verifier\FunctionCallVerifier', $ret );
		}

		/**
		 * @test
		 * it should return null when replacing a static method and not setting a return value
		 */
		public function it_should_retur_null_when_replacing_a_static_method_and_not_setting_a_return_value() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			$ret = FunctionMocker::replace( __NAMESPACE__ . '\SomeClass::staticMethod' );

			$this->assertNull( SomeClass::staticMethod() );
			$ret->wasCalledOnce();
			$ret->wasCalledTimes( 1 );
		}

		/**
		 * @test
		 * it should allow verifying calls on spied static method
		 */
		public function it_should_allow_verifying_calls_on_spied_static_method() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			$spy = FunctionMocker::replace( __NAMESPACE__ . '\SomeClass::staticMethod' );

			SomeClass::staticMethod( 12 );
			SomeClass::staticMethod( 11 );

			$spy->wasCalledTimes( 2 );
			$spy->wasCalledWithTimes( array( 12 ), 1 );
			$spy->wasCalledWithTimes( array( 11 ), 1 );
			$spy->wasNotCalledWith( array( 10 ) );

			$this->setExpectedException( '\PHPUnit_Framework_AssertionFailedError' );
			$spy->wasCalledTimes( 0 );
		}
	}\Patchwork\Interceptor\deployQueue();
