<?php

	namespace tad\FunctionMocker;

	class MockObject {

		/**
		 * @var Checker
		 */
		protected $generator;

		/** @var  ReturnValue */
		protected $returnValue;

		/**
		 * @var Invocation
		 */
		protected $invocation;

		/**
		 * @var bool
		 */
		protected $throw = true;

		public static function from( Checker $generator, ReturnValue $returnValue, Invocation $invocation ) {
			$instance = new self;
			$instance->generator = $generator;
			$instance->returnValue = $returnValue;
			$instance->invocation = $invocation;

			return $instance;
		}

		public function willReturnCallable() {
			return $this->returnValue->isCallable();
		}

		public function wasEvalCreated() {
			return $this->generator->isEvalCreated();
		}

		public function getFunctionName() {
			return $this->generator->getFunctionName();
		}

		public function wasCalledTimes( $times = null ) {
			$callTimes = $this->invocation->getCallTimes();
			$condition = $callTimes === $times;
			if ( ! $condition && $this->throw ) {
				$message = sprintf( '%s was called %d times, %s times expected.', $this->getFunctionName(), $callTimes, $times );
				\PHPUnit_Framework_Assert::fail( $message );
			}

			return $condition;
		}

		public function wasCalledWithTimes( $args, $times ) {
			$callTimes = $this->invocation->getCallTimes( $args );
			$condition = $callTimes === $times;
			if ( ! $condition && $this->throw ) {
				$args = '[' . implode( ', ', $args ) . ']';
				$message = sprintf( '%s was called %d times with %s, %d times expected.', $this->getFunctionName(), $callTimes, $args, $times );
				\PHPUnit_Framework_Assert::fail( $message );
			}

			return $condition;
		}

		public function throwException( $value ) {
			\Arg::_( (bool) $value, 'Value' )->is_bool();

			$this->throw = (bool) $value;
		}

		public function willThrow() {
			return $this->throw;
		}
	}
