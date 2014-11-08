<?php

	namespace tad\FunctionMocker;


	class FunctionMockerTestCase extends \PHPUnit_Framework_TestCase {

		public function run( \PHPUnit_Framework_TestResult $result = null ) {
			parent::run();
			array_map( function ( MockCallLogger $mockCallLoggerInstance ) use ( $result ) {
				try {
					$mockCallLoggerInstance->verify();
				} catch ( \PHPUnit_Framework_AssertionFailedError $e ) {
					$result->addFailure( $this, $e, 1 );
				}
			}, MockCallLogger::$instances );

			return $result;
		}
	}