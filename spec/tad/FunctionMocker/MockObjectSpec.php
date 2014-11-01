<?php

	namespace spec\tad\FunctionMocker;

	use PhpSpec\ObjectBehavior;
	use Prophecy\Argument;
	use tad\FunctionMocker\Checker;
	use tad\FunctionMocker\Invocation;
	use tad\FunctionMocker\ReturnValue;

	class MockObjectSpec extends ObjectBehavior {

		protected $sutClass = 'tad\FunctionMocker\MockObject';

		function it_is_initializable() {
			$this->shouldHaveType( $this->sutClass );
		}

		/**
		 * it can be constructed from function generator and function return value
		 */
		public function it_can_be_constructed__from_function_generator_and_function_return_value( Checker $checker, ReturnValue $returnValue, Invocation $invocation ) {
			$this::__from( $checker, $returnValue, $invocation )->shouldHaveType( $this->sutClass );
		}

		/**
		 * it allows checking if the return value is a callable
		 */
		public function it_allows_checking_if_the_return_value_is_a_callable( Checker $checker, ReturnValue $returnValue, Invocation $invocation ) {
			$returnValue->isCallable()->willReturn( true );
			$this::__from( $checker, $returnValue, $invocation )->__willReturnCallable()->shouldReturn( true );

			$returnValue->isCallable()->willReturn( false );
			$this::__from( $checker, $returnValue, $invocation )->__willReturnCallable()->shouldReturn( false );
		}

		/**
		 * it allows checking if a function has been eval created to mock it
		 */
		public function it_allows_checking_if_a_function_has_been_eval_created_to_mock_it( Checker $checker, ReturnValue $returnValue, Invocation $invocation ) {
			$checker->isEvalCreated()->willReturn( true );
			$this::__from( $checker, $returnValue, $invocation )->__wasEvalCreated()->shouldReturn( true );
			$checker->isEvalCreated()->willReturn( false );
			$this::__from( $checker, $returnValue, $invocation )->__wasEvalCreated()->shouldReturn( false );
		}

		/**
		 * it allows getting the mocked function name
		 */
		public function it_allows_getting_the_mocked_function_name( Checker $checker, ReturnValue $returnValue, Invocation $invocation ) {
			$checker->getFunctionName()->willReturn( 'some' );
			$this::__from( $checker, $returnValue, $invocation )->__getFunctionName()->shouldReturn( 'some' );
		}

		/**
		 * it allows getting the number of calls to the function
		 */
		public function it_allows_getting_the_number_of_calls_to_the_function( Checker $checker, ReturnValue $returnValue, Invocation $invocation ) {
			$sut = $this::__from( $checker, $returnValue, $invocation );
			$invocation->getCallTimes()->willReturn( 3 );
			$sut->wasCalledTimes( 3 )->shouldReturn( true );
		}

		/**
		 * it allows getting the number of calls to the function filtering them by call args
		 */
		public function it_allows_getting_the_number_of_calls_to_the_function_filtering_them_by_call_args( Checker $checker, ReturnValue $returnValue, Invocation $invocation ) {
			$sut = $this::__from( $checker, $returnValue, $invocation );
			$invocation->getCallTimes( [ 'some' ] )->willReturn( 2 );
			$sut->wasCalledWithTimes( [ 'some' ], 2 )->shouldReturn( true );
		}

		/**
		 * it should allow setting if it should throw or not
		 */
		public function it_should_allow_setting_if_it_should_throw_or_not() {
			$this->__willThrow()->shouldReturn( true );
			$this->__throwException( false );
			$this->__willThrow()->shouldReturn( false );
		}

		/**
		 * it will throw if call times is wrong
		 */
		public function it_will_throw_if_call_times_is_wrong( Checker $checker, ReturnValue $returnValue, Invocation $invocation ) {
			$sut = $this::__from( $checker, $returnValue, $invocation );
			$invocation->getCallTimes()->willReturn( 2 );
			$sut->shouldThrow( 'PHPUnit_Framework_AssertionFailedError' )->duringWasCalledTimes( 3 );
		}

		/**
		 * it will throw if call times is wrong per args
		 */
		public function it_will_throw_if_call_times_is_wrong_per_args( Checker $checker, ReturnValue $returnValue, Invocation $invocation ) {
			$sut = $this::__from( $checker, $returnValue, $invocation );
			$invocation->getCallTimes( [ 'some' ] )->willReturn( 2 );
			$sut->shouldThrow( 'PHPUnit_Framework_AssertionFailedError' )->duringWasCalledWithTimes( [ 'some' ], 3 );
		}

		/**
		 * it allows checking for 0 calls using sugar method
		 */
		public function it_allows_checking_for_0_calls_using_sugar_method( Checker $checker, ReturnValue $returnValue, Invocation $invocation ) {
			$sut = $this::__from( $checker, $returnValue, $invocation );
			$invocation->getCallTimes()->willReturn( 0 );
			$sut->wasNotCalled()->shouldReturn( true );
			$invocation->getCallTimes( [ 'some' ] )->willReturn( 0 );
			$sut->wasNotCalledWith( [ 'some' ] )->shouldReturn( true );
		}

	}
