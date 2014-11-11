<?php

	namespace tad\FunctionMocker;


	class TestCase extends \PHPUnit_Framework_TestCase {

		public function assertPostConditions() {
			FunctionMocker::verify();
		}
	}