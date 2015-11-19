<?php
 \Patchwork\Interceptor\deployQueue();/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Base class for all PHPUnit Framework exceptions.
 *
 * Ensures that exceptions thrown during a test run do not leave stray
 * references behind.
 *
 * Every Exception contains a stack trace. Each stack frame contains the 'args'
 * of the called function. The function arguments can contain references to
 * instantiated objects. The references prevent the objects from being
 * destructed (until test results are eventually printed), so memory cannot be
 * freed up.
 *
 * With enabled process isolation, test results are serialized in the child
 * process and unserialized in the parent process. The stack trace of Exceptions
 * may contain objects that cannot be serialized or unserialized (e.g., PDO
 * connections). Unserializing user-space objects from the child process into
 * the parent would break the intended encapsulation of process isolation.
 *
 * @see http://fabien.potencier.org/article/9/php-serialization-stack-traces-and-exceptions
 * @since Class available since Release 3.4.0
 */
class PHPUnit_Framework_Exception extends RuntimeException implements PHPUnit_Exception
{
    /**
     * @var array
     */
    protected $serializableTrace;

    public function __construct($message = '', $code = 0, Exception $previous = null)
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        parent::__construct($message, $code, $previous);

        $this->serializableTrace = $this->getTrace();
        foreach ($this->serializableTrace as $i => $call) {
            unset($this->serializableTrace[$i]['args']);
        }
    }

    /**
     * Returns the serializable trace (without 'args').
     *
     * @return array
     */
    public function getSerializableTrace()
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        return $this->serializableTrace;
    }

    /**
     * @return string
     */
    public function __toString()
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        $string = PHPUnit_Framework_TestFailure::exceptionToString($this);

        if ($trace = PHPUnit_Util_Filter::getFilteredStacktrace($this)) {
            $string .= "\n" . $trace;
        }

        return $string;
    }

    public function __sleep()
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        return array_keys(get_object_vars($this));
    }
}\Patchwork\Interceptor\deployQueue();
