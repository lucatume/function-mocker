<?php
if (!class_exists('GlobalAbstractClass')) {
    /**
     * Class GlobalAbstractClass
     *
     * @since 4.5
     */
    abstract class GlobalAbstractClass
    {
        /**
         * @return string
         */
        public function method_one()
        {
            return 'foo';
        }
        /**
         * Just an abstract public method.
         */
        public abstract function public_method();
        /**
         * Just a protected abstract method.
         *
         * @since 1.0.2
         *
         * @return mixed
         */
        protected abstract function protected_method();
    }
}