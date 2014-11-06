<?php

	namespace tad\FunctionMocker;

	class InstanceSpy implements InvocationMatcher {

		/**
		 * @var \PHPUnit_Framework_MockObject_Invocation
		 */
		public $__invocation;

		/**
		 * @var object
		 */
		public $__object;

		public static function from( \PHPUnit_Framework_MockObject_Matcher_Invocation $invocation, $object ) {
			\Arg::_( $object, 'Object' )->is_object();

			$instance = new self;
			$instance->__invocation = $invocation;
			$instance->__object = $object;

			return $instance;
		}

		public function __call( $name, $args ) {
			$out = call_user_func_array( array( $this->__object, $name ), $args );

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
			$invocations = $this->__invocation->getInvocations();

			\PHPUnit_Framework_Assert::assertCount( $times, $invocations );
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
			$invocations = $this->__invocation->getInvocations();
			$invocations = array_filter( $invocations, function ( $invocation ) use ( $args ) {
				return $invocation->parameters === $args;
			} );

			\PHPUnit_Framework_Assert::assertCount( $times, $invocations );
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
