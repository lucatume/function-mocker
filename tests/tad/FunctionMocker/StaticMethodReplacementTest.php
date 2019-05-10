<?php

namespace tad\FunctionMocker;

use PHPUnit\Framework\TestCase;

class StaticMethodMockingTest extends TestCase
{
    protected function setUp()
    {
        FunctionMocker::setUp();
    }


    protected function tearDown()
    {
        FunctionMocker::tearDown();
    }
}
