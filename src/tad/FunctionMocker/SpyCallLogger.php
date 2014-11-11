<?php

	namespace tad\FunctionMocker;


	class SpyCallLogger implements  CallLogger {

		protected $calls = array();

		public function called( array $args = null ) {
			$this->calls[] = CallTrace::fromArguments( $args );
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

		public function freeze() {
			// TODO: Implement freeze() method.
		}

		public function unfreeze() {
			// TODO: Implement unfreeze() method.
		}
	}
