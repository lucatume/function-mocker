<?php

namespace Acme\Company;

final class FinalClass
{

    /**
     * Method one is public
     */
    public function method_one()
    {
        return 'method_one';
    }

    /**
     * Method three is protected
     */
    protected function method_three()
    {
        return 'method_three';
    }

    /**
     * Method two is private
     */
    private function method_two()
    {
        return 'method_two';
    }
}
