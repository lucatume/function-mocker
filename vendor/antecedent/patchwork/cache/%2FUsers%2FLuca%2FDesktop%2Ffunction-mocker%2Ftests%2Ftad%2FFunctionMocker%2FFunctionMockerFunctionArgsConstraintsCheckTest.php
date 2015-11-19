<?php

namespace tad\FunctionMocker\Tests; \Patchwork\Interceptor\deployQueue();

use tad\FunctionMocker\FunctionMocker as Sut;

class FunctionMockerFunctionArgsConstraintsCheckTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        Sut::setUp();
    }

    public function tearDown()
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        Sut::tearDown();
    }

    public function callArgsAndConstraints()
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        return [
            ['foo', 'foo', true],
            ['foo', Sut::isType('string'), true],
            ['foo', Sut::isType('array'), false],
            [new \stdClass(), Sut::isInstanceOf('\stdClass'), true],
            ['foo', Sut::isInstanceOf('\stdClass'), false]
        ];
    }

    /**
     * @test
     * it should allow verifying the type of args a function is called with
     * @dataProvider callArgsAndConstraints
     */
    public function it_should_allow_verifying_the_type_of_args_a_function_is_called_with($callArg, $expectedArg, $shouldPass)
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        if (!$shouldPass) {
            $this->setExpectedException('\PHPUnit_Framework_AssertionFailedError');
        }
        $func = Sut::replace(__NAMESPACE__ . '\alpha');

        alpha($callArg);

        $func->wasCalledWithOnce([$expectedArg]);
    }

    public function multipleCallArgsAndConstraints()
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        return [
            [['foo', 'foo'], ['foo', 'foo'], true],
            [['foo', 'foo'], [Sut::isType('string'), 'foo'], true],
            [['foo', 'foo'], ['foo', Sut::isType('string')], true],
            [['foo', 'foo'], [Sut::isType('string'), Sut::isType('string')], true],
            [['foo', 'foo'], [Sut::isType('string'), 'baz'], false],
            [['foo', 'foo'], ['baz', Sut::isType('string')], false],
            [['foo', 'foo'], [Sut::isType('string'), Sut::isType('array')], false],
            [[new \stdClass(), 'foo'], [Sut::isInstanceOf('\stdClass'), Sut::isType('string')], true],
            [[new \stdClass(), 'foo'], [Sut::isInstanceOf('\stdClass'), 'foo'], true]
        ];
    }

    /**
     * @test
     * it should allow verifying multiple arguments
     * @dataProvider multipleCallArgsAndConstraints
     */
    public function it_should_allow_verifying_multiple_arguments($callArgs, $expectedArgs, $shouldPass)
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        if (!$shouldPass) {
            $this->setExpectedException('\PHPUnit_Framework_AssertionFailedError');
        }
        $func = Sut::replace(__NAMESPACE__ . '\beta');

        call_user_func_array(__NAMESPACE__ . '\beta', $callArgs);

        $func->wasCalledWithOnce($expectedArgs);
    }
}\Patchwork\Interceptor\deployQueue();

function alpha($arg)
{$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);

}

function beta($arg1, $arg2)
{$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);

}


