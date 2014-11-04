<?php

	namespace spec\tad\FunctionMocker;

	use PhpSpec\ObjectBehavior;
	use Prophecy\Argument;

	class MockRequestParserSpec extends ObjectBehavior {

		function it_is_initializable() {
			$this->shouldHaveType( 'tad\FunctionMocker\MockRequestParser' );
		}

		/**
		 * it allows checking if a request is for a function or a class method
		 */
		public function it_allows_checking_if_a_request_is_for_a_function_or_a_class_method() {
			$sut = $this::on( 'some_function' );
			$sut->isFunction()->shouldReturn( true );
			$sut->isStaticMethod()->shouldReturn( false );
			$sut->isInstanceMethod()->shouldReturn( false );
		}

		/**
		 * it allows checking if a request is for a static or instance method
		 */
		public function it_allows_checking_if_a_request_is_for_a_static_or_instance_method() {
			$sut = $this::on( __NAMESPACE__ . '\SomeClass::instanceMethod' );
			$sut->isFunction()->shouldReturn( false );
			$sut->isMethod()->shouldReturn( true );
			$sut->isStaticMethod()->shouldReturn( false );
			$sut->isInstanceMethod()->shouldReturn( true );
		}

		/**
		 * it allows getting the class name
		 */
		public function it_allows_getting_the_class_name() {
			$sut = $this::on( __NAMESPACE__ . '\SomeClass::instanceMethod' );
			$sut->getClassName()->shouldReturn( 'spec\tad\FunctionMocker\SomeClass' );
		}

		/**
		 * it allows getting the method name
		 */
		public function it_allows_getting_the_method_name() {
			$sut = $this::on( __NAMESPACE__ . '\SomeClass::instanceMethod' );
			$sut->getMethodName()->shouldReturn( 'instanceMethod' );
		}
	}


	class SomeClass {

		public function instanceMethod() {

		}

		public static function staticMethod() {

		}
	}
