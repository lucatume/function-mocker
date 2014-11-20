<?php

	namespace tad\FunctionMocker\Call\Logger;



	class StubCallLogger implements Logger {

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