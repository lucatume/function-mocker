<?php
if (!\interface_exists('Acme\\Company\\NamespacedInterface')) {
    interface NamespacedInterface
    {
        public function method_one();
        public function method_two();
        public function method_three();
    }
}