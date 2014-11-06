<?php

	namespace spec\tad\FunctionMocker;

	use PhpSpec\ObjectBehavior;
	use Prophecy\Argument;

	class InvocationFactorySpec extends ObjectBehavior {

		function it_is_initializable() {
			$this->shouldHaveType( 'tad\FunctionMocker\InvocationFactory' );
		}

		/**
		 * it returns a StubInvocation if no spying and no mocking
		 */
		public function it_returns_a_stub_invocation_if_no_spying_and_no_mocking() {
			$this::make( false, false )->shouldHaveType( 'tad\FunctionMocker\StubInvocation' );
		}

		/**
		 * it returns a SpyInvocation if spying
		 */
		public function it_returns_a_spy_invocation_if_spying() {
			$this::make( true, false )->shouldHaveType( 'tad\FunctionMocker\SpyInvocation' );
		}

		/**
		 * it throws if spying and mocking
		 */
		public function it_throws_if_spying_and_mocking() {
			$this->shouldThrow( '\BadMethodCallException' )->duringMake( true, true );
		}

		/**
		 * it returns a MockInvocation if mocking
		 */
		public function it_returns_a_mock_invocation_if_mocking() {
			$this::make( false, true )->shouldHaveType( 'tad\FunctionMocker\MockInvocation' );
		}
	}
