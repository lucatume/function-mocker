<?php

	namespace tests\tad\FunctionMocker;

	use tad\FunctionMocker\FunctionMocker as Test;
	use tad\FunctionMocker\FunctionMocker;

	class FunctionMockerInstanceMockingTest extends \PHPUnit_Framework_TestCase {

		protected $ns;

		public function setUp() {
			Test::setUp();

			$this->ns = __NAMESPACE__;
		}

		public function tearDown() {
			Test::tearDown();
		}

		/**
		 * @test
		 * it should allow mocking an interface instance method
		 */
		public function it_should_allow_mocking_an_interface_instance_method() {
			$mock = Test::replace( __NAMESPACE__ . '\SomeI::methodOne', 23 );

			$this->assertEquals( 23, $mock->methodOne() );
		}

		/**
		 * @test
		 * it should allow mocking an interface instance method and return a callback
		 */
		public function it_should_allow_mocking_an_interface_instance_method_and_return_a_callback() {
			$mock = Test::replace( __NAMESPACE__ . '\SomeI::methodOne', function () {
				return 23;
			} );

			$this->assertEquals( 23, $mock->methodOne() );
		}

		/**
		 * @test
		 * it should allow mocking an interface instance method and pass arguments to it
		 */
		public function it_should_allow_mocking_an_interface_instance_method_and_pass_arguments_to_it() {
			$mock = Test::replace( __NAMESPACE__ . '\SomeI::methodWithArgs', function ( $string, $int ) {
				return 23 + strlen( $string ) + $int;
			} );

			$this->assertEquals( 28, $mock->methodWithArgs( 'foo', 2 ) );
		}
	}


	interface SomeI {

		public function methodOne();

		public function methodWithArgs( $string, $int );
	}
