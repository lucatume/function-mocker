<?php
namespace Acme\Package;

class StaticMethodReplacementNamespacedTestClass
{
    public static function publicStaticMethodOne()
    {
        return __CLASS__;
    }

    public static function publicStaticMethodTwo()
    {
        return __CLASS__;
    }
}
