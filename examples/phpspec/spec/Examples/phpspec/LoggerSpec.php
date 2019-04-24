<?php

namespace spec\Examples\phpspec;

use Examples\phpspec\Logger;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use tad\FunctionMocker\FunctionMocker as the_function;

class LoggerSpec extends ObjectBehavior {

	public function let() {
		the_function::setUp();
	}

	public function it_is_initializable() {
		$this->shouldHaveType( Logger::class );
	}

	public function it_appends_log_to_existing_hourly_log() {
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

		$this->log( 'Second message' );
	}

	public function it_creates_new_hourly_log() {
		$mockTime = strtotime( '2018-04-21 08:12:45' );
		// stub the internal `time` function
		the_function::time()->willReturn( $mockTime );
		// stub the `get_transient` function, this time to return an empty array
		the_function::get_transient( Argument::type( 'string' ) )
		              ->willReturn( [] );
		// spy the `set_transient` function, calls will be verified in the Assert phase
		the_function::spy( 'set_transient' );

		$this->log( 'Second message' );

		// the spy expectations are explicitly verified
		$expected = [ '12:45' => 'Second message' ];
		the_function::set_transient( Argument::type( 'string' ), $expected, DAY_IN_SECONDS )
		              ->shouldHaveBeenCalled();
	}

	public function it_logs_at_a_specific_time() {
		// stub the `get_transient` function, this time to return an empty array
		the_function::get_transient( Argument::type( 'string' ) )
		              ->willReturn( [] );
		// spy the `set_transient` function, calls will be verified in the Assert phase
		the_function::spy( 'set_transient' );

		$logger = new Logger();
		$logger->log( 'Second message', strtotime( '2018-04-21 08:12:45' ) );

		// the spy expectations are explicitly verified
		$expected = [ '12:45' => 'Second message' ];
		the_function::set_transient( Argument::type( 'string' ), $expected, DAY_IN_SECONDS )
		              ->shouldHaveBeenCalled();

	}

	public function letGo() {
		the_function::tearDown();
	}
}
