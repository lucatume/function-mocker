<?php

namespace tad\FunctionMocker;


if (class_exists('\\PHPunit\\Framework\\TestCase')) {
    class SpoofTestCase extends \PHPunit\Framework\TestCase
    {

    }
} else {
    // PHPUnit < 6.0 support
    class SpoofTestCase extends \PHPUnit_Framework_TestCase
    {

    }
}
