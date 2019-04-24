<?php

namespace Examples\Codeception;

use Prophecy\Argument;
use tad\FunctionMocker\FunctionMocker as the_function;

class LoggerTest extends \Codeception\Test\Unit {

	/**
	 * @var \UnitTester
	 */
	protected $tester;

	protected function _before() {
		the_function::setUp();
	}

	/**
	 * Test logging in an existing log
	 */
	public function test_logging_in_an_existing_log() {
		// Arrange
		$mockTime = strtotime( '2018-04-21 08:12:45' );
		// stub the internal `time` function
		the_function::time()->willReturn( $mockTime );
		// stub the `get_transient` function
		the_function::get_transient( Argument::type( 'string' ) )
		              ->willReturn( [ '12:23' => 'First message' ] );
		// mock the `set_transient` function and set expectations on it
		$expected = [
			'12:23' => 'First message',
			'12:45' => 'Second message',
		];
		the_function::set_transient( Argument::type( 'string' ), $expected, DAY_IN_SECONDS )
		              ->shouldBeCalled();

		// Act
		$logger = new Logger();
		$logger->log( 'Second message' );

		// Assert
		// the mock expectation will be verified in the `tearDown` method
	}

	/**
	 * Test logging in a new hourly log
	 */
	public function test_logging_in_a_new_hourly_log() {
		// Arrange
		$mockTime = strtotime( '2018-04-21 08:12:45' );
		// stub the internal `time` function
		the_function::time()->willReturn( $mockTime );
		// stub the `get_transient` function, this time to return an empty array
		the_function::get_transient( Argument::type( 'string' ) )
		              ->willReturn( [] );
		// spy the `set_transient` function, calls will be verified in the Assert phase
		the_function::spy( 'set_transient' );

		// Act
		$logger = new Logger();
		$logger->log( 'Second message' );

		// Assert
		// the spy expectations are explicitly verified
		$expected = [ '12:45' => 'Second message' ];
		the_function::set_transient( Argument::type( 'string' ), $expected, DAY_IN_SECONDS )
		              ->shouldHaveBeenCalled();
	}

	/**
	 * Test logging at a specific time
	 */
	public function test_logging_at_a_specific_time() {
		// Arrange
		// stub the `get_transient` function, this time to return an empty array
		the_function::get_transient( Argument::type( 'string' ) )
		              ->willReturn( [] );
		// spy the `set_transient` function, calls will be verified in the Assert phase
		the_function::spy( 'set_transient' );

		// Act
		$logger = new Logger();
		$logger->log( 'Second message', strtotime( '2018-04-21 08:12:45' ) );

		// Assert
		// the spy expectations are explicitly verified
		$expected = [ '12:45' => 'Second message' ];
		the_function::set_transient( Argument::type( 'string' ), $expected, DAY_IN_SECONDS )
		              ->shouldHaveBeenCalled();
	}

	protected function _after() {
		the_function::tearDown();
	}
}