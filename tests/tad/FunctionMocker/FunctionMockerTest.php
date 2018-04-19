<?php

namespace tad\FunctionMocker;

use PHPUnit\Framework\TestCase;

class FunctionMockerTest extends TestCase {

	/**
	 * It should allow stubbing a non existing function
	 *
	 * @test
	 */
	public function should_allow_stubbing_a_non_existing_function() {
		FunctionMocker::nonExistingTestFunctionOne( 'foo' )->willReturn( 'bar' );

		$result = nonExistingTestFunctionOne( 'foo' );

		$this->assertEquals( 'bar', $result );
	}

	/**
	 * It should allow stubbing an existing function
	 *
	 * @test
	 */
	public function should_allow_stubbing_an_existing_function() {
		FunctionMocker::testFunctionOne( 'foo' )->willReturn( 'bar' );

		$result = testFunctionOne( 'foo' );

		$this->assertEquals( 'bar', $result );
	}
}
