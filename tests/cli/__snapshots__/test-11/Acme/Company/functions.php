<?php

namespace Acme\Company;

if (!\function_exists('Acme\\Company\\functionOne')) {
    function functionOne()
    {
        return 'functionOne';
    }
}

if (!\function_exists('Acme\\Company\\function_two')) {
    /**
     * This is function_two
     */
    function function_two()
    {
        return 'function_two';
    }
}

