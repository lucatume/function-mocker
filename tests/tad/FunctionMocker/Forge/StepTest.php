<?php
/**
 * Created by IntelliJ IDEA.
 * User: Luca
 * Date: 31/03/15
 * Time: 08:47
 */

namespace tests\tad\FunctionMocker\Forge;


use tad\FunctionMocker\Forge\Step;

class StepTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     * it should throw if passing a non string arg
     */
    public function it_should_throw_if_passing_a_non_string_arg()
    {
        $this->setExpectedException('\Exception');
        new Step(23);
    }

    /**
     * @test
     * it should throw if the class name is a non existing class
     */
    public function it_should_throw_if_the_class_name_is_a_non_existing_class()
    {
        $this->setExpectedException('\Exception');
        new Step('SomeUnrealClass');
    }

}
