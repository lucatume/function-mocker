<?php

	namespace tad\FunctionMocker\Call\Logger;


	class CallLoggerFactory {

		/**
		 * @param $functionName
		 *
		 * @return Logger
		 */
		public static function make( $functionName ) {
			$invocation = new SpyCallLogger();

			return $invocation;
		}
	}