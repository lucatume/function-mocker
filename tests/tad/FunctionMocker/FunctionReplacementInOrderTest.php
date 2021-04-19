<?php

namespace tad\FunctionMocker\Tests;

use tad\FunctionMocker\FunctionMocker;
use tad\FunctionMocker\MockCallLogger;

class FunctionReplacementInOrderTest extends TestCase {

	public function setUp(): void {
		FunctionMocker::setUp();
	}

	public function tearDown(): void {
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
	public function replace_function_should_be_return_values_in_order(array $values) {
		$f = 'Some\Name\Space\func' . uniqid(rand(1, 9999));
		$spy = FunctionMocker::replaceInOrder( $f, $values );

		$this->assertTrue(function_exists($f));
		foreach ( $values as $value ) {
			$this->assertEquals( $value, $f() );
		}
		$spy->wasCalledTimes(count($values));
	}

	/**
	 * Test replace static methods should replace values in order
	 * @dataProvider returnValues
	 */
	public function test_replace_static_methods_should_replace_values_in_order(array $values) {
		$f = 'tad\FunctionMocker\Tests\AClass::staticMethod';
		$spy = FunctionMocker::replaceInOrder( $f, $values );

		foreach ( $values as $value ) {
			$this->assertEquals( $value, $f() );
		}
		$spy->wasCalledTimes(count($values));
	}
}
