<?php

	namespace tad\FunctionMocker;


	class SpyInvocation implements  CallLogger {

		protected $calls = array();

		public function called( array $args = null ) {
			$this->calls[] = InvocationTrace::fromArguments( $args );
		}

		public function getCallTimes( array $args = null ) {
			$calls = $this->calls;
			if ( $args ) {
				$calls = array_filter( $calls, function ( $call ) use ( $args ) {
					return $call->getArguments() === $args;
				} );
			}

			return count( $calls );
		}
	}
