<?php

	namespace tad\FunctionMocker\Tests;

	use tad\FunctionMocker\FunctionMocker;

	class FunctionMockerTest extends \PHPUnit_Framework_TestCase {

		public function setUp() {
			FunctionMocker::load();
		}

		public function tearDown() {
			FunctionMocker::unload();
		}

		/**
		 * @test
		 * it should allow mocking a user defined function
		 */
		public function it_should_allow_mocking_a_user_defined_function() {
			FunctionMocker::mock( 'some_function', 'baz' );
			$this->assertEquals( 'baz', some_function() );
		}

		/**
		 * @test
		 * it should allow mocking an undefined functionj
		 */
		public function it_should_allow_mocking_an_undefined_functionj() {
			FunctionMocker::mock( 'undefined_function', 23 );
			$this->assertEquals( 23, undefined_function() );
		}

		/**
		 * @test
		 * it should allow mocking a function and return a callback
		 */
		public function it_should_allow_mocking_a_function_and_return_a_callback() {
			FunctionMocker::mock( 'some_function', function () {
				return 'some';
			} );
			$this->assertEquals( 'some', some_function() );
		}

		/**
		 * @test
		 * it should allow mocking an undefined function and return a callback
		 */
		public function it_should_allow_mocking_an_undefined_function_and_return_a_callback() {
			FunctionMocker::mock( 'undefined_function', function () {
				return 23;
			} );
			$this->assertEquals( 23, undefined_function() );
		}

		/**
		 * @test
		 * it should allow mocking a user defined function multiple times
		 */
		public function it_should_allow_mocking_a_user_defined_function_multiple_times() {
			FunctionMocker::mock( 'some_function', 'baz' );
			$this->assertEquals( 'baz', some_function() );
			FunctionMocker::mock( 'some_function', 'bar' );
			$this->assertEquals( 'bar', some_function() );
			FunctionMocker::mock( 'some_function', 23 );
			$this->assertEquals( 23, some_function() );
		}

		/**
		 * @test
		 * it should allow mocking an undefined functions multiple times
		 */
		public function it_should_allow_mocking_an_undefined_functions_multiple_times() {
			FunctionMocker::mock( 'undefined_function', 23 );
			$this->assertEquals( 23, undefined_function() );
			FunctionMocker::mock( 'undefined_function', 45 );
			$this->assertEquals( 45, undefined_function() );
			FunctionMocker::mock( 'undefined_function', 'foo' );
			$this->assertEquals( 'foo', undefined_function() );
		}

		/**
		 * @test
		 * it should allow verifying a function was called
		 */
		public function it_should_allow_verifying_a_function_was_called() {
			$mock = FunctionMocker::mock( 'undefined_function', 23 );
			undefined_function();
			undefined_function();
			$mock->wasCalledTimes( 2 );
		}

		/**
		 * @test
		 * it should allow verifying a function was called with args
		 */
		public function it_should_allow_verifying_a_function_was_called_with_args() {
			$mock = FunctionMocker::mock( 'undefined_function' );
			undefined_function( 'foo', 'baz' );
			$mock->wasCalledWithTimes( [ 'foo', 'baz' ], 1 );
		}

		/**
		 * @test
		 * it should allow mocking a defined static class method
		 */
		public function it_should_allow_mocking_a_defined_static_class_method() {
			FunctionMocker::mock( 'AClass::staticMethod', 23 );
			$this->assertEquals( 23, \AClass::staticMethod() );
		}

		/**
		 * @test
		 * it should return an object extending the original one when mocking an instance method
		 */
		public function it_should_return_an_object_extending_the_original_one_when_mocking_an_instance_method() {
			$sut = FunctionMocker::mock( 'AClass::instanceMethod', 23 );

			$this->assertInstanceOf( 'AClass', $sut );
		}

		/**
		 * @test
		 * it should allow mocking a defined class method
		 */
		public function it_should_allow_mocking_a_defined_class_method() {
			$sut = FunctionMocker::mock( 'AClass::instanceMethod', 23 );
			$this->assertEquals( 23, $sut->instanceMethod() );
		}

	}

