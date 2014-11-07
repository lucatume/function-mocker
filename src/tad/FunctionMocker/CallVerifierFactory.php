<?php

	namespace tad\FunctionMocker;

	use tad\FunctionMocker\FunctionCallVerifier;

	class CallVerifierFactory {

		public static function make( ReplacementRequest $request, Checker $checker, ReturnValue $returnValue, CallLogger $callLogger ) {
			if ( $request->isFunction() ) {
				return FunctionCallVerifier::__from( $checker, $returnValue, $callLogger );
			}
			if ( $request->isStaticMethod() ) {
				return StaticMethodCallVerifier::__from( $checker, $returnValue, $callLogger );
			}

			return InstanceMethodCallVerifier::from( $returnValue, $callLogger );
		}
	}
