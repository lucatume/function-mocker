<?php

namespace tad\FunctionMocker\Tests;

use tad\FunctionMocker\FunctionMocker;
use tad\FunctionMocker\MockCallLogger;

class FunctionReplacementInOrderTest extends TestCase {

	public function setUp() {
		FunctionMocker::setUp();
	}

	public function tearDown() {
		FunctionMocker::tearDown();
	}

	public function returnValues()
	{
		return [
			[ [ 'one', 'two', 'three' ] ],
			[ [ 1, 2 ,3 ] ],
			[ [ [ 1 ], [ 2 ], [ 3 ] ] ]
		];
	}
	/**
	 * @test
	 * @dataProvider returnValues
	 */
	public function replace_function_should_be_retunr_values_in_order( $values ) {
		$f = 'Some\Name\Space\func' . uniqid(rand(1, 9999));
		$spy = FunctionMocker::replaceInOrder( $f, $values );

		$this->assertTrue(function_exists($f));
		foreach ( $values as $value ) {
			$this->assertEquals( $value, $f() );
		}
		$spy->wasCalledTimes(count($values));
	}

}
