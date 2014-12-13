<?php

	namespace tad\FunctionMocker\Call\Logger;


	class CallLoggerFactory {

		/**
		 * @param string $functionName
		 *
		 * @return SpyCallLoggerInterface
		 */
		public static function make( $functionName ) {
			\Arg::_( $functionName, 'Function name' )->is_string();

			$invocation = new SpyCallLogger();

			return $invocation;
		}
	}
