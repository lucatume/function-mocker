<?php

	namespace tad\FunctionMocker;


	use tad\FunctionMocker\StubInvocation;

	class InvocationFactory {

		public static function make( $spying, $mocking ) {
			if ( $spying && $mocking ) {
				throw new \BadMethodCallException( 'Either spy or mock, not both.' );
			}
			$invocation = new StubInvocation();
			if ( $spying ) {
				$invocation = new SpyInvocation();
			} else if ( $mocking ) {
				$invocation = new MockInvocation();
			}

			return $invocation;
		}
	}