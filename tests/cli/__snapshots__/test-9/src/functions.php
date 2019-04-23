<?php
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

function withoutDocBlock($a, $b)
{
    throw new RuntimeException('Not implemented.');
}

