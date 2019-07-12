Function Mocker is a function mocking framework powered by [Patchwork][7026-0001] and [Prophecy][7026-0002] born out of my need to stub, mock and spy **functions and static methods** defined by WordPress in the context of unit and integration tests.  
A lot of concepts in this opening, a code example might help understanding.

## A complete PHPUnit example
Supposing I want to test the method `log` of the `Logger` class below:

```php
<?php
// The class under test.
class Logger {
	public function log( $message, $when = null ) {
		$when = $when ? $when : time();

		// log messages on an hourly base
		$transient    = 'log_' . date( 'Y_m_d_H', $when );
		$hourly_log   = (array) get_transient( $transient );

		$hourly_log[ date( 'i:s', $when ) ] = $message;

		set_transient( $transient, $hourly_log, DAY_IN_SECONDS );
		
		// If we have an external logging service then dispatch the message log there too.
		foreach(LoggingServices::loggers() as $externalLogger){
			$externalLogger->log($message);
		}
	}
}
```

In the [PhpUnit](https://phpunit.de/ "PHPUnit – The PHP Testing Framework") bootstrap file:

```php
<?php
// file bootstrap.php
\tad\FunctionMocker\FunctionMocker::init([
	'redefinable-internals' => ['time']
]);
```

The test case itself:

```php
<?php
use \tad\FunctionMocker\FunctionMocker as the_function;

class LoggerTest extends TestCase {

	public function setUp() {
		the_function::setUp();
	}

	/**
	 * Test logging in an existing log
	 */
	public function test_logging_in_an_existing_log() {
		// For the purpose of this test let's say there are no external services.
		the_function::prophesize([LoggingServices,'loggers'])->willReturn([]);
		$mockTime = strtotime( '2018-04-21 08:12:45' );
		// Stub the internal `time` function.
		the_function::time()->willReturn( $mockTime );
		// Stub the `get_transient` function.
		the_function::get_transient( Argument::type( 'string' ) )
		              ->willReturn( [ '12:23' => 'First message' ] );
		// Start spying on the `set_transient` function.
		$expected = [
			'12:23' => 'First message',
			'12:45' => 'Second message',
		];
		$spy = the_function::set_transient( Argument::type( 'string' ), $expected, DAY_IN_SECONDS )
		              ->shouldBeCalled();

		$logger = new Logger();
		$logger->log( 'Second message' );

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
		the_function::spy('set_transient');

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
		the_function::spy('set_transient');

		// Act
		$logger = new Logger();
		$logger->log( 'Second message', strtotime( '2018-04-21 08:12:45' ) );

		// Assert
		// the spy expectations are explicitly verified
		$expected = [ '12:45' => 'Second message' ];
		the_function::set_transient( Argument::type( 'string' ), $expected, DAY_IN_SECONDS )
		              ->shouldHaveBeenCalled();
	}

	public function tearDown() {
		the_function::tearDown( $this );
	}
}
```

You can find this example code [in the PHPUnit example](https://github.com/lucatume/function-mocker/tree/master/examples/phpunit).  
[Go to the installation guide](installation.md) or check out the [alternatives](alternatives.md).

[7026-0001]: http://patchwork2.org/
[7026-0002]: https://github.com/phpspec/prophecy
