<?php

	namespace tad\FunctionMocker;


	use tad\FunctionMocker\StubCallLogger;

	class CallLoggerFactory {

		public static function make( $spying, $mocking ) {
			if ( $spying && $mocking ) {
				throw new \BadMethodCallException( 'Either spy or mock, not both.' );
			}
			$invocation = new StubCallLogger();
			if ( $spying ) {
				$invocation = new SpyCallLogger();
			} else if ( $mocking ) {
				$invocation = new MockCallLogger();
			}

			return $invocation;
		}
	}