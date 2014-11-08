<?php

	namespace spec\tad\FunctionMocker;

	use PhpSpec\ObjectBehavior;
	use Prophecy\Argument;

	class CallLoggerFactorySpec extends ObjectBehavior {

		function it_is_initializable() {
			$this->shouldHaveType( 'tad\FunctionMocker\CallLoggerFactory' );
		}

		/**
		 * it returns a StubCallLogger if no spying and no mocking
		 */
		public function it_returns_a_stub_invocation_if_no_spying_and_no_mocking() {
			$this::make( false, false, 'someFunction' )->shouldHaveType( 'tad\FunctionMocker\StubCallLogger' );
		}

		/**
		 * it returns a SpyCallLogger if spying
		 */
		public function it_returns_a_spy_invocation_if_spying() {
			$this::make( true, false, 'someFucntion' )->shouldHaveType( 'tad\FunctionMocker\SpyCallLogger' );
		}

		/**
		 * it throws if spying and mocking
		 */
		public function it_throws_if_spying_and_mocking() {
			$this->shouldThrow( '\BadMethodCallException' )->duringMake( true, true, 'someFunction' );
		}

		/**
		 * it returns a MockCallLogger if mocking
		 */
		public function it_returns_a_mock_invocation_if_mocking() {
			$this::make( false, true, 'someFunction' )->shouldHaveType( 'tad\FunctionMocker\MockCallLogger' );
		}
	}
