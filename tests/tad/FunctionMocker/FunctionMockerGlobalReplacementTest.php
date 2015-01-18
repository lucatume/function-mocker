<?php

namespace tests\tad\FunctionMocker;


use tad\FunctionMocker\FunctionMocker as Test;

class FunctionMockerGlobalReplacementTest extends \PHPUnit_Framework_TestCase
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
     * it should allow replacing a global object
     */
    public function it_should_allow_replacing_a_global_object()
    {
        $GLOBALS['foo'] = new OneClass();
        $fooReplacement = Test::replaceGlobal('foo', __NAMESPACE__ . '\OneClass::oneMethod', 23);

        global $foo;
        $this->assertEquals(23, $foo->oneMethod());
        $fooReplacement->wasCalledOnce();
    }

    /**
     * @test
     * it should allow replacing an unset global variable
     */
    public function it_should_allow_replacing_an_unset_global_variable()
    {
        $fooReplacement = Test::replaceGlobal('foo', __NAMESPACE__ . '\OneClass::oneMethod', 23);

        global $foo;
        $this->assertEquals(23, $foo->oneMethod());
        $fooReplacement->wasCalledOnce();
    }

    /**
     * @test
     * it should allow replacing a set global variable with a simple value
     */
    public function it_should_allow_replacing_a_set_global_variable_with_a_simple_value()
    {
        $GLOBALS['foo'] = 200;
        $fooReplacement = Test::setGlobal('foo', 23);

        global $foo;
        $this->assertEquals(23, $foo);
    }
}


class OneClass
{

    public function oneMethod()
    {
        return 200;
    }
}
