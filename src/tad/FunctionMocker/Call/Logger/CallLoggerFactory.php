<?php

	namespace tad\FunctionMocker\Call\Logger;


	class CallLoggerFactory {

		/**
		 * @return SpyCallLogger
		 */
		public static function make() {
			$invocation = new SpyCallLogger();

			return $invocation;
		}
	}
