<?php

namespace tad\FunctionMocker;

class FunctionMocker
{

    public static function mock($functionName, $returnValue = null)
    {
        \Arg::_($functionName, 'Function name')->is_string();

        $checker = Checker::fromName($functionName);
        $returnValue = ReturnValue::from($returnValue);
        $invocation = new Invocation();
        $mockObject = MockObject::from($checker, $returnValue, $invocation);

        return $mockObject;
    }
}
