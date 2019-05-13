<?php

namespace Examples\WoocommerceEnv;

class NoFitBox extends Box
{

    public function __construct()
    {
        parent::__construct('no-fit', [ 0, 0, 0, 0 ]);
    }
}
