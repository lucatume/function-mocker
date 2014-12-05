<?php

	namespace tad\FunctionMocker\Call\Logger;


	use tad\FunctionMocker\CallTrace;

	class SpyCallLogger implements LoggerInterface {

		protected $calls = array();

		public function called( array $args = null ) {
			$this->calls[] = CallTrace::fromArguments( $args );
		}

		public function getCallTimes( array $args = null ) {
			$calls = $this->calls;
			if ( $args ) {
				$calls = array_filter( $calls, function ( CallTrace $call ) use ( $args ) {
					return $call->getArguments() === $args;
				} );
			}

			return count( $calls );
		}
	}
