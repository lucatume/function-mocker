<?php

	namespace spec\tad\FunctionMocker;

	use PhpSpec\ObjectBehavior;
	use Prophecy\Argument;

	class FunctionMockerSpec extends ObjectBehavior {

		function it_is_initializable() {
			$this->shouldHaveType( 'tad\FunctionMocker\FunctionMocker' );
		}

		/**
		 * it throws if function name is not a string
		 */
		public function it_throws_if_function_name_is_not_a_string() {
			$this->shouldThrow( '\InvalidArgumentException' )->duringMock( 23 );
		}

	}
