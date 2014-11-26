<?php

	namespace tad\FunctionMocker\Call\Stub;


	use PHPUnit_Framework_MockObject_Matcher_InvokedRecorder;
	use tad\FunctionMocker\Call\CallHandler;
	use tad\FunctionMocker\ReplacementRequest;

	class StubCallHandler implements CallHandler, Stub {

		/**
		 * @param \PHPUnit_Framework_MockObject_Matcher_InvokedRecorder $invokedRecorder
		 *
		 * @return mixed
		 */
		public function setInvokedRecorder( PHPUnit_Framework_MockObject_Matcher_InvokedRecorder $invokedRecorder ) {
			return;
		}

		/**
		 * @param ReplacementRequest $request
		 *
		 * @return mixed
		 */
		public function setRequest( ReplacementRequest $request ) {
			return;
		}

		public function wasCalledWithOnce( array $args = null ) {
			// TODO: Implement wasCalledWithOnce() method.
		}
	}