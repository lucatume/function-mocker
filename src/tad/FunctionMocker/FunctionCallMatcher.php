<?php

	namespace tad\FunctionMocker;

	class FunctionCallMatcher implements CallLogger, CallMatcher {

		public function called( array $args = null ) {
			// TODO: Implement called() method.
		}

		/**
		 * Sets an expectation for the function or method to be called
		 * a number of times no matter the arguments of the call.
		 *
		 * @param int $times
		 *
		 * @throws PHPUnit_Framework_ExpectationFailedException
		 * @return void
		 */
		public function shouldBeCalledTimes( $times ) {
			// TODO: Implement shouldBeCalledTimes() method.
		}

		/**
		 * Sets an expectation for the function or method to be called
		 * a number of times with specific arguments.
		 *
		 * Please note that the suquence of the function or method calls,
		 * and other calls to the function or method with oter arguments
		 * are not taken into account.
		 *
		 * @param array $args The arguments the function or method is expected
		 *                    to be called with.
		 * @param int   $times
		 *
		 * @throws PHPUnit_Framework_ExpectationFailedException
		 * @return void
		 */
		public function shouldBeCalledWithTimes( array $args, $times ) {
			// TODO: Implement shouldBeCalledWithTimes() method.
		}

		/**
		 * Sets an expectation for the function or method not be called
		 * no matter the arguments.
		 *
		 * @throws PHPUnit_Framework_ExpectationFailedException
		 * @return void
		 */
		public function shouldNotBeCalled() {
			// TODO: Implement shouldNotBeCalled() method.
		}

		/**
		 * Sets an expectation for the function or method not be called
		 * with the set arguments.
		 *
		 * @param array $args The arguments the function or method is expected
		 *                    not to be called with.
		 *
		 * @throws PHPUnit_Framework_ExpectationFailedException
		 * @return void
		 */
		public function shouldNotBeCalledWith( array $args ) {
			// TODO: Implement shouldNotBeCalledWith() method.
		}
	}
