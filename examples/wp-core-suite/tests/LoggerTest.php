<?php

namespace Examples\WPCore;

use Prophecy\Argument;
use tad\FunctionMocker\FunctionMocker;

class LoggerTest extends \WP_UnitTestCase {

	protected $backupGlobals = false;

	public function setUp() {
		// Load WordPress...
		parent::setUp();
		// ...then start intercepting calls
		FunctionMocker::setUp();
	}

	/**
	 * Test logging in an existing log
	 */
	public function test_logging_in_an_existing_log() {
		// Arrange
		$mockTime = strtotime( '2018-04-21 08:12:45' );
		// stub the internal `time` function
		FunctionMocker::time()->willReturn( $mockTime );
		// stub the `get_transient` function
		FunctionMocker::get_transient( Argument::type( 'string' ) )
		              ->willReturn( [ '12:23' => 'First message' ] );
		// mock the `set_transient` function and set expectations on it
		$expected = [
			'12:23' => 'First message',
			'12:45' => 'Second message',
		];
		FunctionMocker::set_transient( Argument::type( 'string' ), $expected, DAY_IN_SECONDS )
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
		FunctionMocker::time()->willReturn( $mockTime );
		// stub the `get_transient` function, this time to return an empty array
		FunctionMocker::get_transient( Argument::type( 'string' ) )
		              ->willReturn( [] );
		// spy the `set_transient` function, calls will be verified in the Assert phase
		FunctionMocker::spy( 'set_transient' );

		// Act
		$logger = new Logger();
		$logger->log( 'Second message' );

		// Assert
		// the spy expectations are explicitly verified
		$expected = [ '12:45' => 'Second message' ];
		FunctionMocker::set_transient( Argument::type( 'string' ), $expected, DAY_IN_SECONDS )
		              ->shouldHaveBeenCalled();
	}

	/**
	 * Test logging at a specific time
	 */
	public function test_logging_at_a_specific_time() {
		// Arrange
		// stub the `get_transient` function, this time to return an empty array
		FunctionMocker::get_transient( Argument::type( 'string' ) )
		              ->willReturn( [] );
		// spy the `set_transient` function, calls will be verified in the Assert phase
		FunctionMocker::spy( 'set_transient' );

		// Act
		$logger = new Logger();
		$logger->log( 'Second message', strtotime( '2018-04-21 08:12:45' ) );

		// Assert
		// the spy expectations are explicitly verified
		$expected = [ '12:45' => 'Second message' ];
		FunctionMocker::set_transient( Argument::type( 'string' ), $expected, DAY_IN_SECONDS )
		              ->shouldHaveBeenCalled();
	}

	public function tearDown() {
		// Stop wrapping...
		FunctionMocker::tearDown( $this );
		// ...to let WordPress tearDown smoothly
		parent::tearDown();
	}
}
