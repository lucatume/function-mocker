<?php

	namespace tad\FunctionMocker\Call\Logger;


	class CallLoggerFactory {

		/**
		 * @return SpyCallLoggerInterface
		 */
		public static function make() {
			$invocation = new SpyCallLogger();

			return $invocation;
		}
	}
