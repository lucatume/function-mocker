<?php

	namespace spec\tad\FunctionMocker;

	use PhpSpec\ObjectBehavior;
	use Prophecy\Argument;

	class CallTraceSpec extends ObjectBehavior {

		function it_is_initializable() {
			$this->shouldHaveType( 'tad\FunctionMocker\CallTrace' );
		}

		/**
		 * it allows constructing it from an array of arguments
		 */
		public function it_allows_constructing_it_from_an_array_of_arguments() {
			$this::fromArguments( [ 'some', 23 ] )->getArguments()->shouldReturn( [ 'some', 23 ] );
		}

		/**
		 * it allows constructing it with no arguments
		 */
		public function it_allows_constructing_it_with_no_arguments() {
			$this::fromArguments()->getArguments()->shouldReturn( [ ] );
		}
	}
