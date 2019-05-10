<?php

namespace Acme\Company;

trait NamespacedTrait
{

    /**
     * @return string
     */
    public function method_one()
    {
        return 'one';
    }

    /**
     * @return string
     */
    final protected function method_two()
    {
        return 'two';
    }

    /**
     * @return int
     */
    private function method_three()
    {
        return 23;
    }
}
