<?php

	namespace tad\FunctionMocker\Call\Logger;


	use tad\FunctionMocker\CallLogger;

	class StubCallLogger implements CallLogger {

		public function called( array $args = null ) {
			return;
		}

		public function freeze() {
			// TODO: Implement freeze() method.
		}

		public function unfreeze() {
			// TODO: Implement unfreeze() method.
		}
	}