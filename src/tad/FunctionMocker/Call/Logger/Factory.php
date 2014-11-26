<?php

	namespace tad\FunctionMocker\Call\Logger;


	class Factory {

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