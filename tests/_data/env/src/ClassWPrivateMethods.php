<?php

namespace Acme\Company;

class ClassWPrivateMethods
{

    protected function protected_method()
    {
        return 'protected_method';
    }

    private function private_method()
    {
        return 'private_method';
    }
}
