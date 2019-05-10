<?php

namespace tad\FunctionMocker;

require_once _data_dir('StaticMethodReplacementGlobalTestClass.php');
require_once _data_dir('StaticMethodReplacementNamespacedTestClass.php');

use Acme\Package\StaticMethodReplacementNamespacedTestClass as NamespacedClass;
use PHPUnit\Framework\TestCase;
use StaticMethodReplacementGlobalTestClass as GlobalClass;

class StaticMethodMockingTest extends TestCase
{

    public function classes()
    {
        return [
            'global'     => [ GlobalClass::class, ],
            'namespaced' => [ NamespacedClass::class ],
        ];
    }

    /**
     * It should allow replacing a class public static method
     *
     * @test
     * @dataProvider classes
     */
    public function should_allow_replacing_a_class_public_static_method($class)
    {
        FunctionMocker::replace($class . '::publicStaticMethodOne');

        $this->assertNull(call_user_func([ $class, 'publicStaticMethodOne' ]));
    }

    /**
     * It should allow replacing a class public static method multiple times
     *
     * @test
     * @dataProvider classes
     */
    public function should_allow_replacing_a_class_public_static_method_multiple_times($class)
    {
        FunctionMocker::replace($class . '::publicStaticMethodTwo');

        $this->assertNull(call_user_func([ $class, 'publicStaticMethodTwo' ]));

        FunctionMocker::replace($class . '::publicStaticMethodTwo', 23);

        $this->assertEquals(23, call_user_func([ $class, 'publicStaticMethodTwo' ]));

        FunctionMocker::replace($class . '::publicStaticMethodTwo', function () {
            return 89;
        });

        $this->assertEquals(89, call_user_func([ $class, 'publicStaticMethodTwo' ]));
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
