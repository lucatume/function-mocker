<?php

	/**
	 * @param      $functionName
	 * @param null $returnValue
	 *
	 * @return mixed|\tad\FunctionMocker\Call\Verifier\InstanceMethodCallVerifier|static
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
