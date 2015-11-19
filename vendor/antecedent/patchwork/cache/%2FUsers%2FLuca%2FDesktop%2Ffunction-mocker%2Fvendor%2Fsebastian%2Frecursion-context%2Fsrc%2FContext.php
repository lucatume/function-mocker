<?php
/*
 * This file is part of the Recursion Context package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebastianBergmann\RecursionContext; \Patchwork\Interceptor\deployQueue();

/**
 * A context containing previously processed arrays and objects
 * when recursively processing a value.
 */
final class Context
{
    /**
     * @var array[]
     */
    private $arrays;

    /**
     * @var \SplObjectStorage
     */
    private $objects;

    /**
     * Initialises the context
     */
    public function __construct()
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        $this->arrays  = array();
        $this->objects = new \SplObjectStorage;
    }

    /**
     * Adds a value to the context.
     *
     * @param  array|object             $value The value to add.
     * @return int|string               The ID of the stored value, either as
     *                                        a string or integer.
     * @throws InvalidArgumentException Thrown if $value is not an array or
     *                                        object
     */
    public function add(&$value)
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        if (is_array($value)) {
            return $this->addArray($value);
        }

        else if (is_object($value)) {
            return $this->addObject($value);
        }

        throw new InvalidArgumentException(
            'Only arrays and objects are supported'
        );
    }

    /**
     * Checks if the given value exists within the context.
     *
     * @param  array|object             $value The value to check.
     * @return int|string|false         The string or integer ID of the stored
     *                                        value if it has already been seen, or
     *                                        false if the value is not stored.
     * @throws InvalidArgumentException Thrown if $value is not an array or
     *                                        object
     */
    public function contains(&$value)
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        if (is_array($value)) {
            return $this->containsArray($value);
        }

        else if (is_object($value)) {
            return $this->containsObject($value);
        }

        throw new InvalidArgumentException(
            'Only arrays and objects are supported'
        );
    }

    /**
     * @param  array    $array
     * @return bool|int
     */
    private function addArray(array &$array)
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        $key = $this->containsArray($array);

        if ($key !== false) {
            return $key;
        }

        $this->arrays[] = &$array;

        return count($this->arrays) - 1;
    }

    /**
     * @param  object $object
     * @return string
     */
    private function addObject($object)
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        if (!$this->objects->contains($object)) {
            $this->objects->attach($object);
        }

        return spl_object_hash($object);
    }

    /**
     * @param  array     $array
     * @return int|false
     */
    private function containsArray(array &$array)
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        $keys = array_keys($this->arrays, $array, true);
        $hash = '_Key_' . hash('sha512', microtime(true));

        foreach ($keys as $key) {
            $this->arrays[$key][$hash] = $hash;

            if (isset($array[$hash]) && $array[$hash] === $hash) {
                unset($this->arrays[$key][$hash]);

                return $key;
            }

            unset($this->arrays[$key][$hash]);
        }

        return false;
    }

    /**
     * @param  object       $value
     * @return string|false
     */
    private function containsObject($value)
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        if ($this->objects->contains($value)) {
            return spl_object_hash($value);
        }

        return false;
    }
}\Patchwork\Interceptor\deployQueue();
