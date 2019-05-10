<?php

function withoutComments($a, $b)
{
    return 'withoutComments';
}

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

function withoutDocBlock($a, $b)
{
    return 'withoutDocBlock';
}
