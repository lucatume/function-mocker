<?php

	namespace tad\FunctionMocker\Call\Verifier;


	use PHPUnit_Framework_MockObject_Invocation;
	use tad\FunctionMocker\Call\Logger\LoggerInterface;
	use tad\FunctionMocker\ReturnValue;

	class InstanceMethodCallVerifier extends AbstractVerifier {

		protected $returnValue;
		protected $callLogger;

		public static function from( ReturnValue $returnValue, LoggerInterface $callLogger ) {
			$instance = new self;
			$instance->returnValue = $returnValue;
			$instance->callLogger = $callLogger;

			return $instance;
		}

		public function wasNotCalled() {
			$funcArgs = func_get_args();
			$methodName = ! empty( $funcArgs[0] ) ? $funcArgs[0] : false;
			$methodName = $methodName ? $methodName : $this->request->getMethodName();

			$this->wasCalledTimes( 0, $methodName );
		}

		public function wasNotCalledWith( array $args = null ) {
			$funcArgs = func_get_args();
			$methodName = ! empty( $funcArgs[1] ) ? $funcArgs[1] : false;
			$methodName = $methodName ? $methodName : $this->request->getMethodName();

			$this->wasCalledWithTimes( $args, 0, $methodName );
		}

		public function wasCalledOnce() {
			$funcArgs = func_get_args();
			$methodName = ! empty( $funcArgs[0] ) ? $funcArgs[0] : false;
			$methodName = $methodName ? $methodName : $this->request->getMethodName();

			$this->wasCalledTimes( 1, $methodName );
		}

		public function wasCalledWithOnce( array $args = null ) {
			$funcArgs = func_get_args();
			$methodName = ! empty( $funcArgs[1] ) ? $funcArgs[1] : false;
			$methodName = $methodName ? $methodName : $this->request->getMethodName();

			$this->wasCalledWithTimes( $args, 1, $methodName );
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
			$funcArgs = func_get_args();
			$methodName = ! empty( $funcArgs[1] ) ? $funcArgs[1] : false;
			$methodName = $methodName ? $methodName : $this->request->getMethodName();

			$callTimes = $this->getCallTimesForMethod( $methodName );

			$this->matchCallTimes( $times, $callTimes, $methodName );
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
			$funcArgs = func_get_args();
			$methodName = ! empty( $funcArgs[2] ) ? $funcArgs[2] : false;
			$methodName = $methodName ? $methodName : $this->request->getMethodName();

			$callTimes = $this->getCallTimesWithArgs( $args, $methodName );
			$functionName = $this->request->getMethodName();

			$this->matchCallWithTimes( $args, $times, $functionName, $callTimes );
		}

		/**
		 * @param array  $args
		 *
		 * @param string $methodName
		 *
		 * @return array
		 */
		protected function getCallTimesWithArgs( array $args, $methodName ) {
			$invocations = $this->invokedRecorder->getInvocations();
			$callTimes = 0;
			array_map( function ( \PHPUnit_Framework_MockObject_Invocation_Object $invocation ) use ( &$callTimes, $args, $methodName ) {
				$callTimes += $invocation->parameters === $args && $invocation->methodName === $methodName;
			}, $invocations );

			return $callTimes;
		}

		/**
		 * @param $methodName
		 *
		 * @return int
		 */
		protected function getCallTimesForMethod( $methodName ) {
			$invocations = $this->invokedRecorder->getInvocations();
			$callTimes = 0;
			array_map( function ( PHPUnit_Framework_MockObject_Invocation $invocation ) use ( &$callTimes, $methodName ) {
				$callTimes += $invocation->methodName === $methodName;
			}, $invocations );

			return $callTimes;
		}
	}
