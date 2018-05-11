<?php

namespace tad\FunctionMocker; \Patchwork\CallRerouting\deployQueue();

use PhpSpec\Exception\Exception;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Exception\Prediction\AggregateException;
use Prophecy\Exception\Prophecy\MethodProphecyException;

class FunctionMockerTest extends TestCase {

	/**
	 * It should allow stubbing a non existing function
	 *
	 * @test
	 */
	public function should_allow_stubbing_a_non_existing_function() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\\{closure}":"\\{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\CallRerouting\State::$routes[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\CallRerouting\dispatch($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		FunctionMocker::nonExistingTestFunctionOne( 'foo' )->willReturn( 'bar' );

		$result = nonExistingTestFunctionOne( 'foo' );

		$this->assertEquals( 'bar', $result );
	}

	public function globalNonExistingFunctions() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\\{closure}":"\\{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\CallRerouting\State::$routes[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\CallRerouting\dispatch($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
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
	public function should_allow_stubbing_a_non_existing_function_using_replace( $function ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\\{closure}":"\\{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\CallRerouting\State::$routes[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\CallRerouting\dispatch($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		FunctionMocker::replace( $function, 'foo' )->willReturn( 'bar' );

		$result = \Patchwork\Redefinitions\call_user_func(__NAMESPACE__, $function, 'foo' );

		$this->assertEquals( 'bar', $result );
	}

	/**
	 * It should allow stubbing an existing function
	 *
	 * @test
	 */
	public function should_allow_stubbing_an_existing_function() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\\{closure}":"\\{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\CallRerouting\State::$routes[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\CallRerouting\dispatch($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		FunctionMocker::testFunctionOne( 'foo' )->willReturn( 'bar' );

		$result = testFunctionOne( 'foo' );

		$this->assertEquals( 'bar', $result );
	}

	public function existingFunctions() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\\{closure}":"\\{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\CallRerouting\State::$routes[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\CallRerouting\dispatch($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
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
	public function should_allow_stubbing_existing_functions_using_replace( $function ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\\{closure}":"\\{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\CallRerouting\State::$routes[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\CallRerouting\dispatch($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		FunctionMocker::replace( $function, 'foo' )->willReturn( 'bar' );

		$result = \Patchwork\Redefinitions\call_user_func(__NAMESPACE__, $function, 'foo' );

		$this->assertEquals( 'bar', $result );
	}

	public function nonExistingNamespacedFunctions() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\\{closure}":"\\{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\CallRerouting\State::$routes[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\CallRerouting\dispatch($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
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
	public function should_allow_stubbing_non_existing_namespaced_functions( $function ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\\{closure}":"\\{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\CallRerouting\State::$routes[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\CallRerouting\dispatch($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		FunctionMocker::replace( $function, 'foo' )->willReturn( 'bar' );

		$result = \Patchwork\Redefinitions\call_user_func(__NAMESPACE__, $function, 'foo' );

		$this->assertEquals( 'bar', $result );
	}

	public function existingNamespacedFunctions() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\\{closure}":"\\{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\CallRerouting\State::$routes[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\CallRerouting\dispatch($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
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
	public function should_allow_stubbing_a_namespaced_function( $function ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\\{closure}":"\\{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\CallRerouting\State::$routes[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\CallRerouting\dispatch($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		FunctionMocker::replace( $function, 'foo' )->willReturn( 'bar' );

		$result = \Patchwork\Redefinitions\call_user_func(__NAMESPACE__, $function, 'foo' );

		$this->assertEquals( 'bar', $result );
	}

	/**
	 * It should allow mocking a function
	 *
	 * @test
	 */
	public function should_allow_mocking_a_function() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\\{closure}":"\\{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\CallRerouting\State::$routes[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\CallRerouting\dispatch($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		FunctionMocker::testFunctionFive( 'bar' )->shouldBeCalledTimes( 2 );

		testFunctionFive( 'bar' );

		try {
			FunctionMocker::tearDown();
		} catch ( AggregateException $e ) {
			$this->assertRegExp( '/^.*testFunctionFive.*exactly 2 calls.*$/usm', $e->getMessage() );
		}
	}

	/**
	 * It should allow mocking a namespaced function
	 *
	 * @test
	 */
	public function should_allow_mocking_a_namespaced_function() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\\{closure}":"\\{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\CallRerouting\State::$routes[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\CallRerouting\dispatch($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		FunctionMocker::inNamespace( '\\Test\\Space', function () {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\\{closure}":"\\{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\CallRerouting\State::$routes[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\CallRerouting\dispatch($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			FunctionMocker::testFunctionFive( 'bar' )->shouldBeCalledTimes( 2 );
		} );

		\Test\Space\testFunctionFive( 'bar' );

		try {
			FunctionMocker::tearDown();
		} catch ( AggregateException $e ) {
			$this->assertRegExp( '/^.*testFunctionFive.*exactly 2 calls.*$/usm', $e->getMessage() );
		}
	}

	/**
	 * It should allow spying a function
	 *
	 * @test
	 */
	public function should_allow_spying_a_function() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\\{closure}":"\\{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\CallRerouting\State::$routes[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\CallRerouting\dispatch($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		FunctionMocker::spy( 'testFunctionSix' );

		testFunctionSix( 'bar' );
		try {
			FunctionMocker::testFunctionSix( 'bar' )->shouldHaveBeenCalledTimes( 2 );
		} catch ( MethodProphecyException $e ) {
			$this->assertRegExp( '/^.*exactly 2 calls.*testFunctionSix.*$/usm', $e->getMessage() );
			FunctionMocker::_skipChecks();
		}
	}

	/**
	 * It should allow spying a namespaced function
	 *
	 * @test
	 */
	public function should_allow_spying_a_namespaced_function() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\\{closure}":"\\{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\CallRerouting\State::$routes[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\CallRerouting\dispatch($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		FunctionMocker::inNamespace( '\\Test\\Space', function () {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\\{closure}":"\\{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\CallRerouting\State::$routes[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\CallRerouting\dispatch($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			FunctionMocker::spy( 'testFunctionSix' );
		} );

		\Test\Space\testFunctionSix( 'bar' );

		try {
			FunctionMocker::inNamespace( '\\Test\\Space', function () {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\\{closure}":"\\{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\CallRerouting\State::$routes[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\CallRerouting\dispatch($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
				FunctionMocker::testFunctionSix( 'bar' )->shouldHaveBeenCalledTimes( 2 );
			} );
		} catch ( MethodProphecyException $e ) {
			$this->assertRegExp( '/^.*exactly 2 calls.*testFunctionSix.*$/usm', $e->getMessage() );
			FunctionMocker::_skipChecks();
		}
	}

	/**
	 * It should allow replacing namespaced functions using the __callStatic API
	 *
	 * @test
	 */
	public function should_allow_replacing_namespaced_functions_using_the_call_static_api() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\\{closure}":"\\{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\CallRerouting\State::$routes[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\CallRerouting\dispatch($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		FunctionMocker::
		inNamespace( '\\Test\\Space', function () {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\\{closure}":"\\{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\CallRerouting\State::$routes[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\CallRerouting\dispatch($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			FunctionMocker::testFunctionFive( Argument::type( 'string' ) )->willReturn( 'is string' );
			FunctionMocker::testFunctionFive( Argument::type( 'array' ) )->willReturn( 'is array' );
		} );

		$this->assertEquals( 'is string', \Test\Space\testFunctionFive( 'one' ) );
		$this->assertEquals( 'is array', \Test\Space\testFunctionFive( [ 'foo' => 'bar' ] ) );
	}

	/**
	 * It should allow mocking an internal function
	 *
	 * @test
	 */
	public function should_allow_mocking_an_internal_function() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\\{closure}":"\\{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\CallRerouting\State::$routes[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\CallRerouting\dispatch($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		FunctionMocker::time()->willReturn( 'foo bar' );

		$this->assertEquals( 'foo bar', \Patchwork\Redefinitions\time(__NAMESPACE__) );
	}

	protected function setUp() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\\{closure}":"\\{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\CallRerouting\State::$routes[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\CallRerouting\dispatch($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		FunctionMocker::setUp();
	}

	protected function tearDown() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\\{closure}":"\\{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\CallRerouting\State::$routes[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\CallRerouting\dispatch($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		FunctionMocker::tearDown();
	}
}\Patchwork\CallRerouting\deployQueue();
