<?php

	namespace spec\tad\FunctionMocker;

	use PhpSpec\ObjectBehavior;
	use Prophecy\Argument;
	use tad\FunctionMocker\Checker;
	use tad\FunctionMocker\ReplacementRequest;
	use tad\FunctionMocker\ReturnValue;
	use tad\FunctionMocker\StubCallLogger;

	class CallVerifierFactorySpec extends ObjectBehavior {

		function it_is_initializable() {
			$this->shouldHaveType( 'tad\FunctionMocker\CallVerifierFactory' );
		}

		/**
		 * it will return a FunctionCallVerifier if replacing a function
		 */
		public function it_will_return_a_function_call_verifier_if_replacing_a_function( ReplacementRequest $request, Checker $checker, ReturnValue $returnValue, StubCallLogger $logger ) {
			$request->isFunction()->willReturn( true );
			$this::make( $request, $checker, $returnValue, $logger )
			     ->shouldHaveType( 'tad\FunctionMocker\FunctionCallVerifier' );
		}

		/**
		 * it will return a StaticMethodCallVerifier if replacing a static method
		 */
		public function it_will_return_a_static_method_call_verifier_if_replacing_a_static_method( ReplacementRequest $request, Checker $checker, ReturnValue $returnValue, StubCallLogger $logger ) {

			$request->isFunction()->willReturn( false );
			$request->isStaticMethod()->willReturn( true );
			$this::make( $request, $checker, $returnValue, $logger )
			     ->shouldHaveType( 'tad\FunctionMocker\StaticMethodCallVerifier' );
		}

		/**
		 * it will return an instance method call verifier if replacing an instance method
		 */
		public function it_will_return_an_instance_method_call_verifier_if_replacing_an_instance_method( ReplacementRequest $request, Checker $checker, ReturnValue $returnValue, StubCallLogger $logger ) {
			$request->isFunction()->willReturn( false );
			$request->isStaticMethod()->willReturn( false );
			$request->isInstanceMethod()->willReturn( true );
			$this::make( $request, $checker, $returnValue, $logger )
			     ->shouldHaveType( 'tad\FunctionMocker\InstanceMethodCallVerifier' );
		}
	}
