<?php

namespace tests\tad\FunctionMocker;


use tad\FunctionMocker\FunctionMocker as Test;

class FunctionMockerAssertionWrappingTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Test::setUp();
    }

    public function tearDown()
    {
        Test::tearDown();
    }

    /**
     * @test
     * it should allow wrapping the assertions with no setup
     */
    public function it_should_allow_wrapping_the_assertions_with_no_setup()
    {
        Test::assertTrue(true);
    }
}
