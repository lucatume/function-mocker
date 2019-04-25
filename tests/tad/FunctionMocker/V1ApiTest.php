<?php

namespace tad\FunctionMocker;

use PHPUnit\Framework\TestCase;

class V1ApiTest extends TestCase
{

    /**
     * It should allow replacing a function with a stub returning a scalar value
     *
     * @test
     * @dataProvider scalarValues
     */
    public function should_allow_replacing_a_function_with_a_stub_returning_scalar_value($value)
    {
        FunctionMocker::replace('testFunctionOne', $value);
        $this->assertEquals($value, testFunctionOne());
    }

    /**
     * It should allow replacing a function with a stub returning an object
     *
     * @test
     * @dataProvider complex_objects
     */
    public function should_allow_replacing_a_function_with_a_stub_returning_an_object($object)
    {
        FunctionMocker::replace('testFunctionOne', $object);
        $this->assertSame($object, testFunctionOne());
    }

    /**
     * It should allow replacing a function with a stub calling a callback
     *
     * @test
     */
    public function should_allow_replacing_a_function_with_a_stub_calling_a_callback()
    {
        FunctionMocker::replace('testFunctionSeven', function ($arg1, $arg2) {
            return $arg2 === 'add' ? 23 + (int) $arg1 : $arg1 - 23;
        });
        $this->assertEquals(60, testFunctionSeven(37, 'add'));
        $this->assertEquals(14, testFunctionSeven(37, 'sub'));
    }

    public function scalarValues()
    {
        return [
            'string'       => ['foo'],
            'empty_string' => [''],
            'false'        => [false],
            'true'         => [true],
            'null'         => [null],
            'zero'         => [0],
            'one'          => [1],
            'two'          => [2],
            'string_zero'  => ['0'],
            'string_one'   => ['1'],
            'string_two'   => ['2'],
            'array'        => [23, 89, '2389', 'foo'],
            'empty_array'  => [[]],
        ];
    }

    public function complex_objects()
    {
        return [
            'object'       => [(object) ['foo' => 'bar']],
            'empty_object' => [new \stdClass()],
        ];
    }

    protected function setUp()
    {
        FunctionMocker::setUp();
    }

    protected function tearDown()
    {
        FunctionMocker::tearDown();
    }
}
