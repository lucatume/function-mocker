<?php
	namespace tad\FunctionMocker\Call\Logger;


	interface Logger {

		public function called( array $args = null );
	}
