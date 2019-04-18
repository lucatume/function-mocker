<?php
if (!\class_exists('Acme\\Company\\FinalClass')) {
    class FinalClass
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
        protected function method_two()
        {
            return 'method_two';
        }
    }
}