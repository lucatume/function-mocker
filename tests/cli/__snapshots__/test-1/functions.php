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

