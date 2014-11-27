<?php
	use tad\FunctionMocker\Call\Verifier\Verifier;

	/**
	 * @param      $functionName
	 * @param null $returnValue
	 *
	 * @return Verifier
	 */
	function __replace( $functionName, $returnValue = null ) {
		return tad\FunctionMocker\FunctionMocker::replace( $functionName, $returnValue );
	}

	/**
	 * Shorthand function to access the `FunctionMocker::setUp` method.
	 */
	function setUpFunctionMocker() {
		tad\FunctionMocker\FunctionMocker::setUp();
	}

	/**
	 * Shorthand function to access the `FunctionMocker::tearDown` method.
	 */
	function tearDownFunctionMocker() {
		tad\FunctionMocker\FunctionMocker::tearDown();
	}
