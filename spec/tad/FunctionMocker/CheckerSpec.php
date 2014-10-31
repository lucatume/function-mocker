<?php

	namespace spec\tad\FunctionMocker;

	use PhpSpec\ObjectBehavior;
	use Prophecy\Argument;

	class CheckerSpec extends ObjectBehavior {

		protected $sut_class_name = 'tad\FunctionMocker\Checker';

		function it_is_initializable() {
			$this->shouldHaveType( $this->sut_class_name );
		}

		/**
		 * it can be built using named constructor
		 */
		public function it_can_be_built_using_named_constructor() {
			$this::fromName( 'undefined_function' )->shouldHaveType( $this->sut_class_name );
		}

		/**
		 * it allows getting the function name
		 */
		public function it_allows_getting_the_function_name() {
			$this::fromName( 'undefined_function' )->getFunctionName()->shouldReturn( 'undefined_function' );
		}

		/**
		 * it allows checking if function has been created with eval
		 */
		public function it_allows_checking_if_function_has_been_created_with_eval() {
			$this::fromName( 'undefined_function_1' )->isEvalCreated()->shouldBe( true );
		}

		/**
		 * it will throw if trying to mock a system function
		 */
		public function it_will_throw_if_trying_to_mock_a_system_function() {
			$this->shouldThrow( '\InvalidArgumentException' )->duringFromName( 'array_map' );
		}

	}
