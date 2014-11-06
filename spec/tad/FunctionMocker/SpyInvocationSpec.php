<?php

	namespace spec\tad\FunctionMocker;

	use PhpSpec\ObjectBehavior;
	use Prophecy\Argument;

	class SpyInvocationSpec extends ObjectBehavior {

		function it_is_initializable() {
			$this->shouldHaveType( 'tad\FunctionMocker\SpyInvocation' );
		}

		/**
		 * it allows checking the number of calls
		 */
		public function it_allows_checking_the_number_of_calls() {
			$this->called();
			$this->called();
			$this->getCallTimes()->shouldReturn( 2 );
		}

		/**
		 * it allows filtering the calls by call arguments
		 */
		public function it_allows_filtering_the_calls_by_call_arguments() {
			$this->called( [ 'some' ] );
			$this->called( [ 'foo' ] );
			$this->called( [ 'foo' ] );
			$this->getCallTimes( [ 'foo' ] )->shouldReturn( 2 );
			$this->getCallTimes( [ 'some' ] )->shouldReturn( 1 );
		}
	}
