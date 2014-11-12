<?php

	namespace tad\FunctionMocker;


	class MockCallLogger implements CallLogger, CallMatcher {

		/**
		 * @var array
		 */
		public static $instances = array();
		protected     $verified  = false;
		/**
		 * @var MatchingStrategy
		 */
		protected $callExpectation = null;
		protected $functionName;
		protected $calledTimes     = 0;
		protected $calls           = array();

		public static function from( $functionName ) {
			\Arg::_( $functionName, 'Function name' )->is_string();

			$instance                         = new self;
			$instance->functionName           = $functionName;
			self::$instances[ $functionName ] = $instance;

			return $instance;
		}

		public static function verifyExpectations() {
			if ( ! count( self::$instances ) ) {
				return;
			}
			foreach ( self::$instances as $instance ) {
				try {
					$instance->verify();
				} catch ( \PHPUnit_Framework_AssertionFailedError $fail ) {
					static::reset();
					throw $fail;
				}
			}
		}

		private static function reset() {
			self::$instances = null;
		}


		public function called( array $args = null ) {
			$this->calls[] = $args;
			$this->calledTimes += 1;
		}

		public function verify() {
			if ( ! $this->callExpectation ) {
				return;
			}
			if ( ! $this->callExpectation->matches( $this->calledTimes ) ) {
				$message = sprintf( '%s was expected to be called %s times, was called %d times.', $this->functionName, $this->callExpectation, $this->calledTimes );
				\PHPUnit_Framework_Assert::fail( $message );
			}
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
			\Arg::_( $times, 'Times' )->is_string( $times )->_or()->is_int( $times );

			$this->callExpectation = MatchingStrategyFactory::make( $times );
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