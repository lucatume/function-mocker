<?php

	namespace tad\FunctionMocker;

	class InstanceSpy implements InvocationMatcher {

		/**
		 * @var \PHPUnit_Framework_MockObject_Invocation
		 */
		public $invocation;

		/**
		 * @var object
		 */
		public $object;

		public static function from( \PHPUnit_Framework_MockObject_Matcher_Invocation $invocation, $object ) {
			\Arg::_( $object, 'Object' )->is_object();

			$instance = new self;
			$instance->invocation = $invocation;
			$instance->object = $object;

			return $instance;
		}

		public function __call( $name, $args ) {
			$out = call_user_func_array( array( $this->object, $name ), $args );

			return $out;
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
			\PHPUnit_Framework_Assert::assertCount( $times, $this->invocation->getInvocations() );
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
		}

		/**
		 * Checks that the function or method was not called.
		 *
		 * @return void
		 */
		public function wasNotCalled() {
			// TODO: Implement wasNotCalled() method.
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
			// TODO: Implement wasNotCalledWith() method.
		}

		/**
		 * Checks if a given function or method was called just one time.
		 */
		public function wasCalledOnce() {
			// TODO: Implement wasCalledOnce() method.
		}
	}
