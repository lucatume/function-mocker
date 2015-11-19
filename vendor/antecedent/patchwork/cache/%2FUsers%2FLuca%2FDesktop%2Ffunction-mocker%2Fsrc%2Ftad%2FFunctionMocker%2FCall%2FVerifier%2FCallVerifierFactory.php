<?php

	namespace tad\FunctionMocker\Call\Verifier; \Patchwork\Interceptor\deployQueue();

	use tad\FunctionMocker\Call\Logger\LoggerInterface;
	use tad\FunctionMocker\Checker;
	use tad\FunctionMocker\ReplacementRequest;
	use tad\FunctionMocker\ReturnValue;

	class CallVerifierFactory {

		public static function make( ReplacementRequest $request, Checker $checker, ReturnValue $returnValue, LoggerInterface $callLogger ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			if ( $request->isFunction() ) {
				return FunctionCallVerifier::__from( $checker, $returnValue, $callLogger );
			}
			if ( $request->isStaticMethod() ) {
				return StaticMethodCallVerifier::__from( $checker, $returnValue, $callLogger );
			}

			return InstanceMethodCallVerifier::from( $returnValue, $callLogger );
		}
	}\Patchwork\Interceptor\deployQueue();
