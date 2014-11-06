<?php

	namespace tad\FunctionMocker;

	interface CallMatcher {

		/**
		 * Sets an expectation for the function or method to be called
		 * a number of times no matter the arguments of the call.
		 *
		 * @param int $times
		 *
		 * @throws PHPUnit_Framework_ExpectationFailedException
		 * @return void
		 */
		public function willBeCalledTimes( $times );

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
		public function willBeCalledWithTimes( array $args, $times );

		/**
		 * Sets an expectation for the function or method not be called
		 * no matter the arguments.
		 *
		 * @throws PHPUnit_Framework_ExpectationFailedException
		 * @return void
		 */
		public function willNotBeCalled();

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
		public function willNotBeCalledWith( array $args );
	}