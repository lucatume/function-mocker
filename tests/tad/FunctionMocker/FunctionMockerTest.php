<?php

	namespace tad\FunctionMocker\Tests;

	use tad\FunctionMocker\FunctionMocker;
	use tad\FunctionMocker\TestCase;

	class FunctionMockerTest extends \PHPUnit_Framework_TestCase {

		public function setUp() {
			FunctionMocker::load();
		}

		public function tearDown() {
			FunctionMocker::unload();
		}

		/**
		 * @test
		 * it should return null when stubbin a function
		 */
		public function it_should_return_null_when_stubbin_a_function() {
			$ret = FunctionMocker::stub( __NAMESPACE__ . '\someFunction' );

			$this->assertNull( $ret );
			$this->assertNull( someFunction() );
		}

		/**
		 * @test
		 * it should return the set return value when stubbing a function and setting a return value
		 */
		public function it_should_return_the_set_return_value_when_stubbing_a_function_and_setting_a_return_value() {
			$ret = FunctionMocker::stub( __NAMESPACE__ . '\someFunction', 23 );

			$this->assertEquals( 23, someFunction() );
		}

		/**
		 * @test
		 * it should return the callback when stubbing a function and setting a closure return value
		 */
		public function it_should_return_the_callback_when_stubbing_a_function_and_setting_a_closure_return_value() {
			$ret = FunctionMocker::stub( __NAMESPACE__ . '\someFunction', function ( $value ) {
				return $value + 1;
			} );

			$this->assertEquals( 24, someFunction( 23 ) );

			$ret = FunctionMocker::stub( __NAMESPACE__ . '\someFunction', function ( $a, $b ) {
				return $a + $b;
			} );

			$this->assertEquals( 23, someFunction( 11, 12 ) );
		}

		/**
		 * @test
		 * it should return null when stubbing a static method
		 */
		public function it_should_return_null_when_stubbing_a_static_method() {
			$ret = FunctionMocker::stub( __NAMESPACE__ . '\SomeClass::staticMethod' );

			$this->assertNull( $ret );
		}

		/**
		 * @test
		 * it should allow setting various return values when stubbing a static method
		 */
		public function it_should_allow_setting_various_return_values_when_stubbin_a_static_method() {
			$ret = FunctionMocker::stub( __NAMESPACE__ . '\SomeClass::staticMethod', 23 );

			$this->assertEquals( 23, SomeClass::staticMethod() );

			$ret = FunctionMocker::stub( __NAMESPACE__ . '\SomeClass::staticMethod', function ( $a ) {
				return $a + 1;
			} );

			$this->assertEquals( 24, SomeClass::staticMethod( 23 ) );

			$ret = FunctionMocker::stub( __NAMESPACE__ . '\SomeClass::staticMethod', function ( $a, $b ) {
				return $a + $b;
			} );

			$this->assertEquals( 24, SomeClass::staticMethod( 23, 1 ) );
		}

		/**
		 * @test
		 * it should return an object instance when stubbing an instance method
		 */
		public function it_should_return_an_object_instance_when_stubbing_an_instance_method() {
			$ret = FunctionMocker::stub( __NAMESPACE__ . '\SomeClass::instanceMethod', 23 );

			$this->assertInstanceOf( __NAMESPACE__ . '\SomeClass', $ret );
			$this->assertEquals( 23, $ret->instanceMethod() );
		}

		/**
		 * @test
		 * it should allow setting various return values when stubbing an instance method
		 */
		public function it_should_allow_setting_various_return_values_when_stubbing_an_instance_method() {
			$ret = FunctionMocker::stub( __NAMESPACE__ . '\SomeClass::instanceMethod', 23 );

			$this->assertEquals( 23, $ret->instanceMethod() );

			$ret = FunctionMocker::stub( __NAMESPACE__ . '\SomeClass::instanceMethod', function ( $a ) {
				return $a + 1;
			} );

			$this->assertEquals( 24, $ret->instanceMethod( 23 ) );

			$ret = FunctionMocker::stub( __NAMESPACE__ . '\SomeClass::instanceMethod', function ( $a, $b ) {
				return $a + $b;
			} );

			$this->assertEquals( 24, $ret->instanceMethod( 23, 1 ) );
		}

		/**
		 * @test
		 * it should return a matcher when spying a function
		 */
		public function it_should_return_a_matcher_when_spying_a_function() {
			$ret = FunctionMocker::spy( __NAMESPACE__ . '\someFunction' );

			$this->assertInstanceOf( 'tad\FunctionMocker\FunctionCallVerifier', $ret );
		}

		/**
		 * @test
		 * it should return null when spying a function and not setting a return value
		 */
		public function it_should_pass_when_spying_a_function() {
			$ret = FunctionMocker::spy( __NAMESPACE__ . '\someFunction' );

			$this->assertNull( someFunction() );
			$ret->wasCalledOnce();
			$ret->wasCalledTimes( 1 );
		}

		/**
		 * @test
		 * it should allow setting various return values when spying a function
		 */
		public function it_should_allow_setting_various_return_values_when_spying_a_function() {
			$ret = FunctionMocker::spy( __NAMESPACE__ . '\someFunction', 23 );

			$this->assertEquals( 23, someFunction() );

			$ret = FunctionMocker::spy( __NAMESPACE__ . '\someFunction', function ( $value ) {
				return $value + 1;
			} );

			$this->assertEquals( 24, someFunction( 23 ) );

			$ret = FunctionMocker::spy( __NAMESPACE__ . '\someFunction', function ( $a, $b ) {
				return $a + $b;
			} );

			$this->assertEquals( 23, someFunction( 11, 12 ) );
		}

		/**
		 * @test
		 * it should return a matcher when spying a static method
		 */
		public function it_should_return_a_matcher_when_spying_a_static_method() {
			$ret = FunctionMocker::spy( __NAMESPACE__ . '\SomeClass::staticMethod' );

			$this->assertInstanceOf( 'tad\FunctionMocker\FunctionCallVerifier', $ret );
		}

		/**
		 * @test
		 * it should return null when spying a static method and not setting a return value
		 */
		public function it_should_retur_null_when_spying_a_static_method_and_not_setting_a_return_value() {
			$ret = FunctionMocker::spy( __NAMESPACE__ . '\SomeClass::staticMethod' );

			$this->assertNull( SomeClass::staticMethod() );
			$ret->wasCalledOnce();
			$ret->wasCalledTimes( 1 );
		}

		/**
		 * @test
		 * it should allow setting various return values when spying a static method
		 */
		public function it_should_allow_setting_various_return_values_when_spying_a_static_method() {
			$ret = FunctionMocker::spy( __NAMESPACE__ . '\SomeClass::staticMethod', 23 );

			$this->assertEquals( 23, SomeClass::staticMethod() );

			$ret = FunctionMocker::spy( __NAMESPACE__ . '\SomeClass::staticMethod', function ( $value ) {
				return $value + 1;
			} );

			$this->assertEquals( 24, SomeClass::staticMethod( 23 ) );

			$ret = FunctionMocker::spy( __NAMESPACE__ . '\SomeClass::staticMethod', function ( $a, $b ) {
				return $a + $b;
			} );

			$this->assertEquals( 23, SomeClass::staticMethod( 11, 12 ) );
		}

		/**
		 * @test
		 * it should return a spy object when spying an instance method
		 */
		public function it_should_return_a_spy_object_when_spying_an_instance_method() {
			$ret = FunctionMocker::spy( __NAMESPACE__ . '\SomeClass::instanceMethod' );

			$this->assertInstanceOf( 'tad\FunctionMocker\InstanceSpy', $ret );
		}

		/**
		 * @test
		 * it should return nulll when spying an instance method and not settin any return value
		 */
		public function it_should_return_null_when_spying_an_instance_method_and_not_setting_any_return_value() {
			$ret   = FunctionMocker::spy( __NAMESPACE__ . '\SomeClass::instanceMethod' );
			$value = $ret->instanceMethod();

			$this->assertNull( $value );
		}

		/**
		 * @test
		 * it should allow setting various return values on a spied instance method
		 */
		public function it_should_allow_setting_various_return_values_on_a_spied_instance_method() {
			$ret = FunctionMocker::spy( __NAMESPACE__ . '\SomeClass::instanceMethod', 23 );

			$this->assertEquals( 23, $ret->instanceMethod() );

			$ret = FunctionMocker::spy( __NAMESPACE__ . '\SomeClass::instanceMethod', function ( $a ) {
				return $a + 1;
			} );

			$this->assertEquals( 24, $ret->instanceMethod( 23 ) );

			$ret = FunctionMocker::spy( __NAMESPACE__ . '\SomeClass::instanceMethod', function ( $a, $b ) {
				return $a + $b;
			} );

			$this->assertEquals( 24, $ret->instanceMethod( 23, 1 ) );
		}

		/**
		 * @test
		 * it should allow verifying calls on spied function
		 */
		public function it_should_allow_verifying_calls_on_spied_function() {
			$spy = FunctionMocker::spy( __NAMESPACE__ . '\someFunction' );

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
			$spy = FunctionMocker::spy( __NAMESPACE__ . '\SomeClass::staticMethod' );

			SomeClass::staticMethod( 12 );
			SomeClass::staticMethod( 11 );

			$spy->wasCalledTimes( 2 );
			$spy->wasCalledWithTimes( array( 12 ), 1 );
			$spy->wasCalledWithTimes( array( 11 ), 1 );
			$spy->wasNotCalledWith( array( 10 ) );

			$this->setExpectedException( '\PHPUnit_Framework_AssertionFailedError' );
			$spy->wasCalledTimes( 0 );
		}

		/**
		 * @test
		 * it should allow verifying calls on spied instance method
		 */
		public function it_should_allow_verifying_calls_on_spied_instance_method() {
			$spy = FunctionMocker::spy( __NAMESPACE__ . '\SomeClass::instanceMethod' );

			$spy->instanceMethod( 12 );
			$spy->instanceMethod( 11 );

			$spy->wasCalledTimes( 2 );
			$spy->wasCalledWithTimes( array( 12 ), 1 );
			$spy->wasCalledWithTimes( array( 11 ), 1 );
			$spy->wasNotCalledWith( array( 10 ) );

			$this->setExpectedException( '\PHPUnit_Framework_AssertionFailedError' );
			$spy->wasCalledTimes( 0 );
		}

		/**
		 * @test
		 * it should return a CallMatcher instance when mocking a function
		 */
		public function it_should_return_a_call_matcher_instance_when_mocking_a_function() {
			$this->assertInstanceOf( '\tad\FunctionMocker\CallMatcher', FunctionMocker::mock( __NAMESPACE__ . '\someFunction' ) );
		}

		public function functionExpectations() {
			return array(
				// times, calls, shouldThrow
				array( 3, 2, true ),
				array( 3, 3, false )
			);
		}

		/**
		 * @test
		 * it should allow setting expectations on mocked functions
		 * @dataProvider functionExpectations
		 */
		public function it_should_allow_setting_expectations_on_mocked_functions( $times, $calls, $shouldThrow ) {
			if ( $shouldThrow ) {
				$this->setExpectedException( '\PHPUnit_Framework_AssertionFailedError' );
			}

			FunctionMocker::mock( __NAMESPACE__ . '\someFunction' )->shouldBeCalledTimes( $times );

			for ( $i = 0; $i < $calls; $i ++ ) {
				someFunction();
			}

			// test only
			FunctionMocker::verify();
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


	function someFunction( $value1 = 0, $value2 = 0 ) {
		return 'foo';
	}

	function anotherFunction() {

	}

