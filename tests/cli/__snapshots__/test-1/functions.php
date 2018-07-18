<?php

if (!function_exists('withoutComments')) {
    function withoutComments($a, $b)
    {
    }
}

if (!function_exists('withDocBlock')) {
    /**
     * This function as a doc-block.
     *
     * @param string $a
     * @param int $b
     */
    function withDocBlock($a, $b)
    {
    }
}

if (!function_exists('withoutDocBlock')) {
    function withoutDocBlock($a, $b)
    {
    }
}

