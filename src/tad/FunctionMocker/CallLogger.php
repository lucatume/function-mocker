<?php
	namespace tad\FunctionMocker;


	interface CallLogger {

		public function called( array $args = null );
	}