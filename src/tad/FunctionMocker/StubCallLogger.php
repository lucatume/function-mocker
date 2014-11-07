<?php

	namespace tad\FunctionMocker;


	use tad\FunctionMocker\CallLogger;

	class StubCallLogger implements CallLogger {

		public function called( array $args = null ) {
			return;
		}
	}