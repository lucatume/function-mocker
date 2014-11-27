<?php

	namespace tad\FunctionMocker\Call\Verifier;


	use tad\FunctionMocker\Call\Logger\Logger;
	use tad\FunctionMocker\ReturnValue;

	class InstanceMethodCallVerifier extends AbstractVerifier {

		protected $returnValue;
		protected $callLogger;

		//todo: probably get rid of the returnValue and the callLogger
		public static function from( ReturnValue $returnValue, Logger $callLogger ) {
			$instance = new self;
			$instance->returnValue = $returnValue;
			$instance->callLogger = $callLogger;

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
			$args = func_get_args();
			$methodName = ! empty( $args[1] ) ? $args[1] : false;
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
			$callTimes = $this->getCallTimesWithArgs( $args );
			$functionName = $this->request->getMethodName();

			$this->matchCallWithTimes( $args, $times, $functionName, $callTimes );
		}

		/**
		 * @param array $args
		 *
		 * @return array
		 */
		protected function getCallTimesWithArgs( array $args ) {
			$invocations = $this->invokedRecorder->getInvocations();
			$callTimes = 0;
			array_map( function ( \PHPUnit_Framework_MockObject_Invocation_Object $invocation ) use ( &$callTimes, &$args ) {
				$callTimes += $invocation->parameters === $args;
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
			array_map( function ( $invocation ) use ( &$callTimes, $methodName ) {
				$callTimes += $invocation->methodName === $methodName;
			}, $invocations );

			return $callTimes;
		}
	}