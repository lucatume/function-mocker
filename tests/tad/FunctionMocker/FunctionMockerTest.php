<?php

	namespace tad\FunctionMocker\Tests;

	use tad\FunctionMocker\FunctionMocker;
	use tad\FunctionMocker\MockCallLogger;
	use tad\FunctionMocker\TestCase;

	class FunctionMockerTest extends \PHPUnit_Framework_TestCase {

		public function setUp() {
			FunctionMocker::setUp();
		}

		public function tearDown() {
			FunctionMocker::tearDown();
		}

		/**
		 * @test
		 * it should return a Verifier object when replacing a function
		 */
		public function it_should_return_null_when_stubbin_a_function() {
			$ret = FunctionMocker::replace( __NAMESPACE__ . '\someFunction' );

			$this->assertInstanceOf( 'tad\FunctionMocker\Call\Verifier\Verifier', $ret );
		}

		/**
		 * @test
		 * it should return the set return value when replacing a function and setting a return value
		 */
		public function it_should_return_the_set_return_value_when_replacing_a_function_and_setting_a_return_value() {
			$ret = FunctionMocker::replace( __NAMESPACE__ . '\someFunction', 23 );

			$this->assertEquals( 23, someFunction() );
		}

		/**
		 * @test
		 * it should return the callback return value when replacing a function and setting a callback return value
		 */
		public function it_should_return_the_callback_return_value_when_replacing_a_function_and_setting_a_callback_return_value() {
			$ret = FunctionMocker::replace( __NAMESPACE__ . '\someFunction', function ( $value ) {
				return $value + 1;
			} );

			$this->assertEquals( 24, someFunction( 23 ) );

			$ret = FunctionMocker::replace( __NAMESPACE__ . '\someFunction', function ( $a, $b ) {
				return $a + $b;
			} );

			$this->assertEquals( 23, someFunction( 11, 12 ) );
		}

		/**
		 * @test
		 * it should return a Verifier when replacing a static method
		 */
		public function it_should_return_a_verifier_when_replacing_a_static_method() {
			$ret = FunctionMocker::replace( __NAMESPACE__ . '\SomeClass::staticMethod' );

			$this->assertInstanceOf( 'tad\FunctionMocker\Call\Verifier\Verifier', $ret );
		}

		/**
		 * @test
		 * it should allow setting various return values when replacing a static method
		 */
		public function it_should_allow_setting_various_return_values_when_replacing_a_static_method() {
			$ret = FunctionMocker::replace( __NAMESPACE__ . '\SomeClass::staticMethod', 23 );

			$this->assertEquals( 23, SomeClass::staticMethod() );

			$ret = FunctionMocker::replace( __NAMESPACE__ . '\SomeClass::staticMethod', function ( $a ) {
				return $a + 1;
			} );

			$this->assertEquals( 24, SomeClass::staticMethod( 23 ) );

			$ret = FunctionMocker::replace( __NAMESPACE__ . '\SomeClass::staticMethod', function ( $a, $b ) {
				return $a + $b;
			} );

			$this->assertEquals( 24, SomeClass::staticMethod( 23, 1 ) );
		}

		/**
		 * @test
		 * it should return null when replacing a function and not setting a return value
		 */
		public function it_should_return_null_when_replacing_a_function_and_not_setting_a_return_value() {
			$ret = FunctionMocker::replace( __NAMESPACE__ . '\someFunction' );

			$this->assertNull( someFunction() );
			$ret->wasCalledOnce();
			$ret->wasCalledTimes( 1 );
		}

		/**
		 * @test
		 * it should allow setting various return values when spying a function
		 */
		public function it_should_allow_setting_various_return_values_when_spying_a_function() {
			$ret = FunctionMocker::replace( __NAMESPACE__ . '\someFunction', 23 );

			$this->assertEquals( 23, someFunction() );

			$ret = FunctionMocker::replace( __NAMESPACE__ . '\someFunction', function ( $value ) {
				return $value + 1;
			} );

			$this->assertEquals( 24, someFunction( 23 ) );

			$ret = FunctionMocker::replace( __NAMESPACE__ . '\someFunction', function ( $a, $b ) {
				return $a + $b;
			} );

			$this->assertEquals( 23, someFunction( 11, 12 ) );
		}

		/**
		 * @test
		 * it should return a matcher when spying a static method
		 */
		public function it_should_return_a_matcher_when_spying_a_static_method() {
			$ret = FunctionMocker::replace( __NAMESPACE__ . '\SomeClass::staticMethod' );

			$this->assertInstanceOf( 'tad\FunctionMocker\Call\Verifier\FunctionCallVerifier', $ret );
		}

		/**
		 * @test
		 * it should return null when replacing a static method and not setting a return value
		 */
		public function it_should_retur_null_when_replacing_a_static_method_and_not_setting_a_return_value() {
			$ret = FunctionMocker::replace( __NAMESPACE__ . '\SomeClass::staticMethod' );

			$this->assertNull( SomeClass::staticMethod() );
			$ret->wasCalledOnce();
			$ret->wasCalledTimes( 1 );
		}

		/**
		 * @test
		 * it should allow verifying calls on spied function
		 */
		public function it_should_allow_verifying_calls_on_spied_function() {
			$spy = FunctionMocker::replace( __NAMESPACE__ . '\someFunction' );

			someFunction( 12 );
			someFunction( 11 );

			$spy->wasCalledTimes( 2 );
			$spy->wasCalledWithTimes( array( 12 ), 1 );
			$spy->wasCalledWithTimes( array( 11 ), 1 );
			$spy->wasNotCalledWith( array( 10 ) );

			$this->setExpectedException( '\PHPUnit_Framework_AssertionFailedError' );
			$spy->wasCalledTimes( 0 );
		}

		/**
		 * @test
		 * it should allow verifying calls on spied static method
		 */
		public function it_should_allow_verifying_calls_on_spied_static_method() {
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

		public function exactExpectations() {
			return array(
				// times, calls, shouldThrow
				array( 3, 2, true ),
				array( 3, 3, false ),
				array( 0, 1, true ),
				array( 0, 0, false ),
				array( 1, 3, true ),
			);
		}


		public function gt3Expectations() {
			return array(
				array( 1, true ),
				array( 3, true ),
				array( 4, false ),
				array( 0, true )
			);
		}

		public function atLeast3Expectations() {
			return array(
				array( 1, true ),
				array( 3, false ),
				array( 4, false ),
				array( 0, true )
			);
		}

		public function lessThan3Expectations() {
			return array(
				array( 0, false ),
				array( 1, false ),
				array( 3, true ),
				array( 4, true )
			);
		}

		public function atMost2Expectations() {
			return array(
				array( 1, false ),
				array( 1, false ),
				array( 0, false ),
				array( 3, true )
			);
		}

		public function not3Expectations() {
			return array(
				array( 1, false ),
				array( 3, true ),
				array( 0, false ),
				array( 4, false )
			);
		}

		public function exact3Expectations() {
			return array(
				array( 1, true ),
				array( 0, true ),
				array( 3, false ),
				array( 4, true )
			);
		}

	}


	class SomeClass {

		public static function staticMethod( $a = null, $b = null ) {
			return "foo baz";
		}

		public function instanceMethod() {
			return 'some value';
		}
	}


	class SomeClassExtension extends SomeClass {

		public function instanceMethod() {
			return 'foo';
		}
	}


	class AnotherClass {

		public function someMethod() {
		}
	}


	function someFunction( $value1 = 0, $value2 = 0 ) {
		return 'foo';
	}

	function anotherFunction() {

	}

