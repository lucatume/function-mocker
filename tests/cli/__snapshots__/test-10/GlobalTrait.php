<?php


if (!trait_exists('GlobalTrait')) {
    trait GlobalTrait
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
        protected final function method_two()
        {
            return 'two';
        }
        /**
         * @return int
         */
        protected function method_three()
        {
            return 23;
        }
    }
}