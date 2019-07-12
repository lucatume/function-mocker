<?php

namespace Examples\PHPUnit;

use Examples\PHPUnit\Legacy\LoggingServices;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use tad\FunctionMocker\FunctionMocker as the_function;

class LoggerTest extends TestCase
{

    public function setUp()
    {
        the_function::setUp();
    }

    /**
     * Test logging in an existing log
     */
    public function test_logging_in_an_existing_log()
    {
        $mockTime = strtotime('2018-04-21 08:12:45');
        // Stub the internal `time` function.
        the_function::time()->willReturn($mockTime);
        // Mock the `get_transient` function.
        the_function::get_transient(Argument::type('string'))
                    ->willReturn([ '12:23' => 'First message' ]);

        /** @var \Prophecy\Prophecy\MethodProphecy $get_transient */
        $get_transient = the_function::set_transient('log_2018_04_21_08', [
            '12:23' => 'First message',
            '12:45' => 'Second message',
        ], DAY_IN_SECONDS);
        // For the purpose of this test let's have no external logging services.
        the_function::ofClass([ LoggingServices::class, 'getLoggers' ])->willReturn([]);

        $logger = new Logger();
        $logger->log('Second message');

        $get_transient->shouldHaveBeenCalledOnce();
    }

    //    /**
    //     * Test logging in a new hourly log
    //     */
    //    public function test_logging_in_a_new_hourly_log()
    //    {
    //        // Arrange
    //        $mockTime = strtotime('2018-04-21 08:12:45');
    //        // stub the internal `time` function
    //        the_function::time()->willReturn($mockTime);
    //        // stub the `get_transient` function, this time to return an empty array
    //        the_function::get_transient(Argument::type('string'))
    //                      ->willReturn([]);
    //        // spy the `set_transient` function, calls will be verified in the Assert phase
    //        the_function::spy('set_transient');
    //
    //        // Act
    //        $logger = new Logger();
    //        $logger->log('Second message');
    //
    //        // Assert
    //        // the spy expectations are explicitly verified
    //        $expected = [ '12:45' => 'Second message' ];
    //        the_function::set_transient(Argument::type('string'), $expected, DAY_IN_SECONDS)
    //                      ->shouldHaveBeenCalled();
    //    }
    //
    //    /**
    //     * Test logging at a specific time
    //     */
    //    public function test_logging_at_a_specific_time()
    //    {
    //        // Arrange
    //        // stub the `get_transient` function, this time to return an empty array
    //        the_function::get_transient(Argument::type('string'))
    //                      ->willReturn([]);
    //        // spy the `set_transient` function, calls will be verified in the Assert phase
    //        the_function::spy('set_transient');
    //
    //        // Act
    //        $logger = new Logger();
    //        $logger->log('Second message', strtotime('2018-04-21 08:12:45'));
    //
    //        // Assert
    //        // the spy expectations are explicitly verified
    //        $expected = [ '12:45' => 'Second message' ];
    //        the_function::set_transient(Argument::type('string'), $expected, DAY_IN_SECONDS)
    //                      ->shouldHaveBeenCalled();
    //    }

    public function tearDown()
    {
        the_function::tearDown($this);
    }
}
