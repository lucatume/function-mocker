<?php

	namespace tad\FunctionMocker;

	class MockObject {

		private $__matcher;

		public function __construct() {

		}

		public function __init( Matcher $matcher = null ) {
			$this->__matcher = $matcher ? $matcher : new \tad\FunctionMocker\Matcher();
		}

		public function __call( $name, $args ) {
			return call_user_func_array( array( $this->__matcher, $name ), $args );
		}
	}