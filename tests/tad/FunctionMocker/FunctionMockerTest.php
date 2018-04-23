<?php

namespace tad\FunctionMocker;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Exception\Prediction\AggregateException;

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

	public function globalNonExistingFunctions() {
		return [
			'no-leading-slash'        => [ 'nonExistingTestFunctionTwo' ],
			'leading-slash'           => [ '\\nonExistingTestFunctionThree' ],
			'no-escape-leading-slash' => [ '\nonExistingTestFunctionFour' ],
		];
	}

	/**
	 * It should allow stubbing a non existing function using replace
	 *
	 * @test
	 *
	 * @dataProvider globalNonExistingFunctions
	 */
	public function should_allow_stubbing_a_non_existing_function_using_replace( $function ) {
		FunctionMocker::replace( $function, 'foo' )->willReturn( 'bar' );

		$result = call_user_func( $function, 'foo' );

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

	public function existingFunctions() {
		return [
			'no-leading-slash'        => [ 'testFunctionTwo' ],
			'leading-slash'           => [ '\\testFunctionThree' ],
			'no-escape-leading-slash' => [ '\testFunctionFour' ],
		];
	}

	/**
	 * It should allow stubbing existing functions using replace
	 *
	 * @test
	 *
	 * @dataProvider existingFunctions
	 */
	public function should_allow_stubbing_existing_functions_using_replace( $function ) {
		FunctionMocker::replace( $function, 'foo' )->willReturn( 'bar' );

		$result = call_user_func( $function, 'foo' );

		$this->assertEquals( 'bar', $result );
	}

	public function nonExistingNamespacedFunctions() {
		return [
			'no-leading-slash'           => [ 'Test\\Space\\nonExistingTestFunctionOne' ],
			'leading-slash'              => [ '\\Test\\Space\\nonExistingTestFunctionTwo' ],
			'no-escape-no-leading-slash' => [ 'Test\Space\nonExistingTestFunctionThree' ],
			'no-escape-leading-slash'    => [ '\Test\Space\nonExistingTestFunctionFour' ],
		];
	}

	/**
	 * It should allow stubbing non existing namespaced functions
	 *
	 * @dataProvider nonExistingNamespacedFunctions
	 *
	 * @test
	 */
	public function should_allow_stubbing_non_existing_namespaced_functions( $function ) {
		FunctionMocker::replace( $function, 'foo' )->willReturn( 'bar' );

		$result = call_user_func( $function, 'foo' );

		$this->assertEquals( 'bar', $result );
	}

	public function existingNamespacedFunctions() {
		return [
			'no-leading-slash'           => [ 'Test\\Space\\testFunctionOne' ],
			'leading-slash'              => [ '\\Test\\Space\\testFunctionTwo' ],
			'no-escape-no-leading-slash' => [ 'Test\Space\testFunctionThree' ],
			'no-escape-leading-slash'    => [ '\Test\Space\testFunctionFour' ],
		];
	}

	/**
	 * It should allow stubbing a namespaced function
	 *
	 * @test
	 *
	 * @dataProvider existingNamespacedFunctions
	 */
	public function should_allow_stubbing_a_namespaced_function( $function ) {
		FunctionMocker::replace( $function, 'foo' )->willReturn( 'bar' );

		$result = call_user_func( $function, 'foo' );

		$this->assertEquals( 'bar', $result );
	}

	/**
	 * It should allow spying a function
	 *
	 * @test
	 */
	public function should_allow_spying_a_function() {
		FunctionMocker::testFunctionFive( 'bar' )->shouldBeCalledTimes( 2 );

		testFunctionFive( 'bar' );

		try {
			FunctionMocker::tearDown();
		}
		catch ( AggregateException $e ) {
			$this->assertRegExp( '/^.*testFunctionFive.*exactly 2 calls.*$/us', $e->getMessage() );
		}
	}

	/**
	 * It should allow replacing namespaced functions using the __callStatic API
	 *
	 * @test
	 */
	public function should_allow_replacing_namespaced_functions_using_the_call_static_api() {
		FunctionMocker::
		inNamespace( '\\Test\\Space', function () {
			FunctionMocker::testFunctionFive( Argument::type( 'string' ) )->willReturn( 'is string' );
			FunctionMocker::testFunctionFive( Argument::type( 'array' ) )->willReturn( 'is array' );
		} );

		$this->assertEquals( 'is string', \Test\Space\testFunctionFive( 'one' ) );
		$this->assertEquals( 'is array', \Test\Space\testFunctionFive( [ 'foo' => 'bar' ] ) );
	}

	protected function setUp() {
		FunctionMocker::setUp();
	}

	protected function tearDown() {
		FunctionMocker::tearDown();
	}
}
