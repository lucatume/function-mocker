<?php

	namespace tad\FunctionMocker\Tests;


	class functionsTest extends \PHPUnit_Framework_TestCase {

		public function setUp() {
			setUpFunctionMocker();
		}

		public function tearDown() {
			tearDownFunctionMocker();
		}

		/**
		 * @test
		 * it should allow replacing a function using the function
		 */
		public function it_should_allow_replacing_a_function_using_the_function() {
			__replace( 'some_function_123', 23 );

			$this->assertEquals( 23, some_function_123() );
		}

		/**
		 * @test
		 * it should allow replacing a static method using the function
		 */
		public function it_should_allow_replacing_a_static_method_using_the_function() {
			__replace( __NAMESPACE__ . '\AClass::staticMethod', 23 );

			$this->assertEquals( 23, AClass::staticMethod() );
		}

		/**
		 * @test
		 * it should allow replacing an instance method using the function
		 */
		public function it_should_allow_replacing_an_instance_method_using_the_function() {
			$replacement = __replace( __NAMESPACE__ . '\AClass::instanceMethod', 23 );

			$this->assertEquals( 23, $replacement->instanceMethod() );
		}
	}