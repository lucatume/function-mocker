<?php

	namespace tad\FunctionMocker;

	class MockObject {

		/**
		 * @var Checker
		 */
		protected $__generator;

		/** @var  ReturnValue */
		protected $__returnValue;

		/**
		 * @var Invocation
		 */
		protected $__invocation;

		/**
		 * @var bool
		 */
		protected $__throw = true;

		public static function __from( Checker $generator, ReturnValue $returnValue, Invocation $invocation ) {
			$instance = new self;
			$instance->__generator = $generator;
			$instance->__returnValue = $returnValue;
			$instance->__invocation = $invocation;

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

		public function wasCalledTimes( $times ) {
			\Arg::_( $times, 'Times' )->is_int();

			$callTimes = $this->__invocation->getCallTimes();
			$condition = $callTimes === $times;
			if ( ! $condition && $this->__throw ) {
				$message = sprintf( '%s was called %d times, %s times expected.', $this->__getFunctionName(), $callTimes, $times );
				\PHPUnit_Framework_Assert::fail( $message );
			}

			\PHPUnit_Framework_Assert::assertTrue( $condition );

			return $condition;
		}

		public function wasCalledWithTimes( array $args = array(), $times ) {
			\Arg::_( $times, 'Times' )->is_int();

			$callTimes = $this->__invocation->getCallTimes( $args );
			$condition = $callTimes === $times;
			if ( ! $condition && $this->__throw ) {
				$args = '[' . implode( ', ', $args ) . ']';
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

		public function wasNotCalled() {
			return $this->wasCalledTimes( 0 );
		}

		public function wasNotCalledWith( array $args = null ) {
			return $this->wasCalledWithTimes( $args, 0 );
		}
	}
