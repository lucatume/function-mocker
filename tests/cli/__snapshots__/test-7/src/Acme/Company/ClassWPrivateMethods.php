<?php
if (!\class_exists('Acme\\Company\\ClassWPrivateMethods')) {
    class ClassWPrivateMethods
    {
        protected function protected_method()
        {
            return 'protected_method';
        }
        protected function private_method()
        {
            return 'private_method';
        }
    }
}