<?php

namespace tad\FunctionMocker;

use function Patchwork\redefine;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Exception\Prediction\AggregateException;
use Prophecy\Exception\Prophecy\MethodProphecyException;

class FunctionMockerTest extends TestCase
{

    /**
     * It should allow stubbing a non existing function
     *
     * @test
     */
    public function should_allow_stubbing_a_non_existing_function()
    {
        FunctionMocker::nonExistingTestFunctionOne('foo')->willReturn('bar');

        $result = nonExistingTestFunctionOne('foo');

        $this->assertEquals('bar', $result);
    }

    public function globalNonExistingFunctions()
    {
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
    public function should_allow_stubbing_a_non_existing_function_using_replace($function)
    {
        FunctionMocker::prophesize($function, 'foo')->willReturn('bar');

        $result = call_user_func($function, 'foo');

        $this->assertEquals('bar', $result);
    }

    /**
     * It should allow stubbing an existing function
     *
     * @test
     */
    public function should_allow_stubbing_an_existing_function()
    {
        FunctionMocker::testFunctionOne('foo')->willReturn('bar');

        $result = testFunctionOne('foo');

        $this->assertEquals('bar', $result);
    }

    public function existingFunctions()
    {
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
    public function should_allow_stubbing_existing_functions_using_replace($function)
    {
        FunctionMocker::prophesize($function, 'foo')->willReturn('bar');

        $result = call_user_func($function, 'foo');

        $this->assertEquals('bar', $result);
    }

    public function nonExistingNamespacedFunctions()
    {
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
    public function should_allow_stubbing_non_existing_namespaced_functions($function)
    {
        FunctionMocker::prophesize($function, 'foo')->willReturn('bar');

        $result = call_user_func($function, 'foo');

        $this->assertEquals('bar', $result);
    }

    public function existingNamespacedFunctions()
    {
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
    public function should_allow_stubbing_a_namespaced_function($function)
    {
        FunctionMocker::prophesize($function, 'foo')->willReturn('bar');

        $result = call_user_func($function, 'foo');

        $this->assertEquals('bar', $result);
    }

    /**
     * It should allow mocking a function
     *
     * @test
     */
    public function should_allow_mocking_a_function()
    {
        FunctionMocker::testFunctionFive('bar')->shouldBeCalledTimes(2);

        testFunctionFive('bar');

        try {
            FunctionMocker::tearDown();
        } catch (AggregateException $e) {
            $this->assertRegExp('/^.*testFunctionFive.*exactly 2 calls.*$/usm', $e->getMessage());
        }
    }

    /**
     * It should allow mocking a namespaced function
     *
     * @test
     */
    public function should_allow_mocking_a_namespaced_function()
    {
        FunctionMocker::inNamespace('\\Test\\Space', function () {
            FunctionMocker::testFunctionFive('bar')->shouldBeCalledTimes(2);
        });

        \Test\Space\testFunctionFive('bar');

        try {
            FunctionMocker::tearDown();
        } catch (AggregateException $e) {
            $this->assertRegExp('/^.*testFunctionFive.*exactly 2 calls.*$/usm', $e->getMessage());
        }
    }

    /**
     * It should allow spying a function
     *
     * @test
     */
    public function should_allow_spying_a_function()
    {
        FunctionMocker::spy('testFunctionSix');

        testFunctionSix('bar');
        try {
            FunctionMocker::testFunctionSix('bar')->shouldHaveBeenCalledTimes(2);
        } catch (MethodProphecyException $e) {
            $this->assertRegExp('/^.*exactly 2 calls.*testFunctionSix.*$/usm', $e->getMessage());
            FunctionMocker::skipChecks();
        }
    }

    /**
     * It should allow spying a namespaced function
     *
     * @test
     */
    public function should_allow_spying_a_namespaced_function()
    {
        FunctionMocker::inNamespace('\\Test\\Space', function () {
            FunctionMocker::spy('testFunctionSix');
        });

        \Test\Space\testFunctionSix('bar');

        try {
            FunctionMocker::inNamespace('\\Test\\Space', function () {
                FunctionMocker::testFunctionSix('bar')->shouldHaveBeenCalledTimes(2);
            });
        } catch (MethodProphecyException $e) {
            $this->assertRegExp('/^.*exactly 2 calls.*testFunctionSix.*$/usm', $e->getMessage());
            FunctionMocker::skipChecks();
        }
    }

    /**
     * It should allow replacing namespaced functions using the __callStatic API
     *
     * @test
     */
    public function should_allow_replacing_namespaced_functions_using_the_call_static_api()
    {
        FunctionMocker::
        inNamespace('\\Test\\Space', function () {
            FunctionMocker::testFunctionFive(Argument::type('string'))->willReturn('foo');
        });

        $this->assertEquals('foo', \Test\Space\testFunctionFive('foo'));
        $this->assertEquals('foo', \Test\Space\testFunctionFive('bar'));
    }

    /**
     * It should allow mocking an internal function
     *
     * @test
     */
    public function should_allow_mocking_an_internal_function()
    {
        FunctionMocker::time()->willReturn('foo bar');

        $this->assertEquals('foo bar', time());
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
