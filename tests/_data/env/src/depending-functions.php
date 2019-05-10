<?php

function depending_function_one(SomeInputClass $input)
{
}

function depending_function_two(\Acme\Service\API $api)
{
}

function using_a_fully_qualified_class()
{
    $o = new \Foo\Bar\Baz;
}

function using_global_class()
{
    $o = new Something;
}
