<?php
 \Patchwork\Interceptor\deployQueue();/*
 * This file is part of the PHPUnit_MockObject package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represents a non-static invocation.
 *
 * @since Class available since Release 1.0.0
 */
class PHPUnit_Framework_MockObject_Invocation_Object extends PHPUnit_Framework_MockObject_Invocation_Static
{
    /**
     * @var object
     */
    public $object;

    /**
     * @param string $className
     * @param string $methodname
     * @param array  $parameters
     * @param object $object
     * @param object $cloneObjects
     */
    public function __construct($className, $methodName, array $parameters, $object, $cloneObjects = false)
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        parent::__construct($className, $methodName, $parameters, $cloneObjects);
        $this->object = $object;
    }
}\Patchwork\Interceptor\deployQueue();
