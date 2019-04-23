<?php
if (!\class_exists('Acme\\Company\\ClassWFinalMethods')) {
    class ClassWFinalMethods
    {
        public function final_public_method()
        {
            return 'final_public_method';
        }
        protected function final_protected_method()
        {
            return 'final_protected_method';
        }
    }
}