<?php

	namespace tad\FunctionMocker\Call\Verifier;

	class AbstractVerifier implements Verifier {

		public function wasCalledTimes( $times ) {
			throw new \Exception( 'Method not implemented' );
		}

		public function wasCalledWithTimes( array $args = array(), $times ) {
			throw new \Exception( 'Method not implemented' );
		}

		public function wasNotCalled() {
			throw new \Exception( 'Method not implemented' );
		}

		public function wasNotCalledWith( array $args = null ) {
			throw new \Exception( 'Method not implemented' );
		}

		public function wasCalledOnce() {
			throw new \Exception( 'Method not implemented' );
		}
	}