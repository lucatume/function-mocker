<?php

	namespace tad\FunctionMocker;

	class FunctionCallVerifier implements CallVerifier {

		/**
		 * @var Checker
		 */
		protected $__generator;

		/** @var  ReturnValue */
		protected $__returnValue;

		/**
		 * @var SpyCallLogger
		 */
		protected $__callLogger;

		/**
		 * @var bool
		 */
		protected $__throw = true;

		public static function __from( Checker $generator, ReturnValue $returnValue, CallLogger $callLogger ) {
			$instance                = new self;
			$instance->__generator   = $generator;
			$instance->__returnValue = $returnValue;
			$instance->__callLogger  = $callLogger;

			return $instance;
		}

		public function __willReturnCallable() {
			return $this->__returnValue->isCallable();
		}

		public function __wasEvalCreated() {
			return $this->__generator->isEvalCreated();
		}

		public function __getFunctionName() {
			return $this->__generator->getFunctionName();
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
			\Arg::_( $times, 'Times' )->is_int();

			$callTimes = $this->__callLogger->getCallTimes();
			$condition = $callTimes === $times;
			if ( ! $condition && $this->__throw ) {
				$message = sprintf( '%s was called %d times, %s times expected.', $this->__getFunctionName(), $callTimes, $times );
				\PHPUnit_Framework_Assert::fail( $message );
			}

			\PHPUnit_Framework_Assert::assertTrue( $condition );

			return $condition;
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
			\Arg::_( $times, 'Times' )->is_int();

			$callTimes = $this->__callLogger->getCallTimes( $args );
			$condition = $callTimes === $times;
			if ( ! $condition && $this->__throw ) {
				$args    = '[' . implode( ', ', $args ) . ']';
				$message = sprintf( '%s was called %d times with %s, %d times expected.', $this->__getFunctionName(), $callTimes, $args, $times );
				\PHPUnit_Framework_Assert::fail( $message );
			}

			\PHPUnit_Framework_Assert::assertTrue( $condition );

			return $condition;
		}

		public function __throwException( $value ) {
			\Arg::_( (bool) $value, 'Value' )->is_bool();

			$this->__throw = (bool) $value;
		}

		public function __willThrow() {
			return $this->__throw;
		}

		/**
		 * Checks that the function or method was not called.
		 *
		 * @return void
		 */
		public function wasNotCalled() {
			return $this->wasCalledTimes( 0 );
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
			return $this->wasCalledWithTimes( $args, 0 );
		}

		/**
		 * Checks if a given function or method was called just one time.
		 */
		public function wasCalledOnce() {
			return $this->wasCalledTimes( 1 );
		}
	}
