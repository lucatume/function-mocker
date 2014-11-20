<?php

	namespace tad\FunctionMocker\Call\Verifier;


	class InstanceMethodCallVerifier extends AbstractVerifier {

		protected $returnValue;
		protected $callLogger;

		public static function from( ReturnValue $returnValue, CallLogger $callLogger ) {
			$instance              = new self;
			$instance->returnValue = $returnValue;
			$instance->callLogger  = $callLogger;

			return $instance;
		}

		/**
		 * Checks if the function or method was called the specified number
		 * of times.
		 *
		 * @param  int $times
		 *
		 * @return void
		 */
		public function wasCalledTimes( $times ) {
			return;
		}

		/**
		 * Checks if the function or method was called with the specified
		 * arguments a number of times.
		 *
		 * @param  array $args
		 * @param  int   $times
		 *
		 * @return void
		 */
		public function wasCalledWithTimes( array $args = array(), $times ) {
			return;
		}

		/**
		 * Checks that the function or method was not called.
		 *
		 * @return void
		 */
		public function wasNotCalled() {
			return;
		}

		/**
		 * Checks that the function or method was not called with
		 * the specified arguments.
		 *
		 * @param  array $args
		 *
		 * @return void
		 */
		public function wasNotCalledWith( array $args = null ) {
			return;
		}

		/**
		 * Checks if a given function or method was called just one time.
		 */
		public function wasCalledOnce() {
			return;
		}
	}