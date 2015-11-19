<?php
/**
 * Created by IntelliJ IDEA.
 * User: Luca
 * Date: 31/03/15
 * Time: 23:20
 */

namespace tad\FunctionMocker\Forge; \Patchwork\Interceptor\deployQueue();


use tad\FunctionMocker\ReplacementRequest;

class InstanceMethodRequest extends ReplacementRequest
{

    public static function instance($class)
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        \Arg::_($class, 'Class name')->is_string()->assert(class_exists($class) || interface_exists($class) || trait_exists($class), 'Class must be a defined one');
        $instance = new self;
        $instance->requestClassName = $class;
        $instance->isStaticMethod = false;
        $instance->isInstanceMethod = true;
        $instance->isFunction = false;
        $instance->isMethod = true;
        $instance->methodName = '';

        return $instance;
    }
}\Patchwork\Interceptor\deployQueue();