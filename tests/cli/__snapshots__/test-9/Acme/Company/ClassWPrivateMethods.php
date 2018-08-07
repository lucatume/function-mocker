<?php


if (!\class_exists('Acme\\Company\\ClassWPrivateMethods')) {
    class ClassWPrivateMethods
    {
        protected function protected_method()
        {
            throw new RuntimeException('Not implemented.');
        }
        protected function private_method()
        {
            throw new RuntimeException('Not implemented.');
        }
    }
}