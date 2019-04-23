<?php
if (!function_exists('withoutComments')) {
    function withoutComments($a, $b)
    {
        return 'withoutComments';
    }
}

if (!function_exists('withDocBlock')) {
    /**
     * This function has a doc-block.
     *
     * @param string $a
     * @param int    $b
     *
     * @return string
     */
    function withDocBlock($a, $b)
    {
        return 'withDocBlock';
    }
}

if (!function_exists('withoutDocBlock')) {
    function withoutDocBlock($a, $b)
    {
        return 'withoutDocBlock';
    }
}

if (!function_exists('depending_function_one')) {
    function depending_function_one(SomeInputClass $input)
    {
    }
}

if (!function_exists('depending_function_two')) {
    function depending_function_two(\Acme\Service\API $api)
    {
    }
}

if (!function_exists('using_a_fully_qualified_class')) {
    function using_a_fully_qualified_class()
    {
        $o = new \Foo\Bar\Baz();
    }
}

if (!function_exists('using_global_class')) {
    function using_global_class()
    {
        $o = new Something();
    }
}

