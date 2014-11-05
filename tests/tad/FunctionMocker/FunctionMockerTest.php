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
		 * it should return null when stubbin a function
		 */
		public function it_should_return_null_when_stubbin_a_function() {
			$ret = FunctionMocker::stub( __NAMESPACE__ . '\someFunction', 23 );

			$this->assertNull( $ret );
			$this->assertEquals( 23, someFunction() );
		}

		/**
		 * @test
		 * it should return null when stubbing a static method
		 */
		public function it_should_return_null_when_stubbing_a_static_method() {
			$ret = FunctionMocker::stub( __NAMESPACE__ . '\SomeClass::staticMethod', 23 );

			$this->assertNull( $ret );
			$this->assertEquals( 23, SomeClass::staticMethod() );
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
		 * it should return a matcher when spying a function
		 */
		public function it_should_return_a_matcher_when_spying_a_function() {
			$ret = FunctionMocker::spy( __NAMESPACE__ . '\someFunction' );

			$this->assertInstanceOf( 'tad\FunctionMocker\FunctionMatcher', $ret );
		}

		/**
		 * @test
		 * it should pass when spying a function
		 */
		public function it_should_pass_when_spying_a_function() {
			$ret = FunctionMocker::spy( __NAMESPACE__ . '\someFunction' );

			$this->assertEquals( 'foo', someFunction() );
			$ret->wasCalledOnce();
			$ret->wasCalledTimes( 1 );
		}

		/**
		 * @test
		 * it should return a matcher when spying a static method
		 */
		public function it_should_return_a_matcher_when_spying_a_static_method() {
			$ret = FunctionMocker::spy( __NAMESPACE__ . '\SomeClass::staticMethod' );

			$this->assertInstanceOf( 'tad\FunctionMocker\FunctionMatcher', $ret );
		}

		/**
		 * @test
		 * it should pass when spying a static method
		 */
		public function it_should_pass_when_spying_a_static_method() {
			$ret = FunctionMocker::spy( __NAMESPACE__ . '\SomeClass::staticMethod' );

			$this->assertEquals( 'foo baz', SomeClass::staticMethod() );
			$ret->wasCalledOnce();
			$ret->wasCalledTimes( 1 );
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
		 * it should pass when spying an instance method
		 */
		public function it_should_pass_when_spying_an_instance_method() {
			$ret = FunctionMocker::spy( __NAMESPACE__ . '\SomeClass::instanceMethod' );
			$value = $ret->instanceMethod();

			$this->assertEquals( 'some value', $value );
		}

	}


	class SomeClass {

		public static function staticMethod() {
			return "foo baz";
		}

		public function instanceMethod() {
			return 'some value';
		}
	}


	function someFunction() {
		return 'foo';
	}

