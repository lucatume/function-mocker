<?php

	namespace tad\FunctionMocker\Call\Logger; \Patchwork\Interceptor\deployQueue();


	class CallLoggerFactory {

		/**
		 * @param string $functionName
		 *
		 * @return LoggerInterface
		 */
		public static function make( $functionName ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			\Arg::_( $functionName, 'Function name' )->is_string();

			$invocation = new SpyCallLogger();

			return $invocation;
		}
	}\Patchwork\Interceptor\deployQueue();
