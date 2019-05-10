<?php

namespace Acme\Company;

class ClassWFinalMethods
{

    final public function final_public_method()
    {
        return 'final_public_method';
    }

    final protected function final_protected_method()
    {
        return 'final_protected_method';
    }
}
