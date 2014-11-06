<?php

	namespace tad\FunctionMocker;


	use tad\FunctionMocker\CallLogger;

	class StubInvocation implements CallLogger {

		public function called( array $args = null ) {
			return;
		}
	}