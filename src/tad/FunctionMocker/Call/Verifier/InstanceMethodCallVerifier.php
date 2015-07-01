<?php

namespace tad\FunctionMocker\Call\Verifier;


use tad\FunctionMocker\Call\Logger\LoggerInterface;
use tad\FunctionMocker\ReturnValue;

class InstanceMethodCallVerifier extends AbstractVerifier
{

    protected $returnValue;
    protected $callLogger;

    public static function from(ReturnValue $returnValue, LoggerInterface $callLogger)
    {
        $instance = new self;
        $instance->returnValue = $returnValue;
        $instance->callLogger = $callLogger;

        return $instance;
    }

    public function wasNotCalled()
    {
        $funcArgs = func_get_args();
        $this->realWasCalledTimes(0, $funcArgs);
    }

    /**
     * @param $times
     * @param $funcArgs
     */
    private function realWasCalledTimes($times, $funcArgs)
    {
        $methodName = $this->request->getMethodName() ?: $funcArgs[0];
        $callTimes = $this->getCallTimesWithArgs($methodName);
        $this->matchCallTimes($times, $callTimes, $methodName);
    }

    /**
     * @param array $args
     *
     * @param string $methodName
     *
     * @return array
     */
    protected function getCallTimesWithArgs($methodName, array $args = null)
    {
        $invocations = $this->invokedRecorder->getInvocations();
        $callTimes = 0;
        array_map(function (\PHPUnit_Framework_MockObject_Invocation_Object $invocation) use (&$callTimes, $args, $methodName) {
            if (is_array($args)) {
                $callTimes += $this->compareName($invocation, $methodName) && $this->compareArgs($invocation, $args);
            } else {
                $callTimes += $this->compareName($invocation, $methodName);
            }
        }, $invocations);

        return $callTimes;
    }

    /**
     * @param \PHPUnit_Framework_MockObject_Invocation_Object $invocation
     * @param                                                 $methodName
     *
     * @return bool
     */
    private function compareName(\PHPUnit_Framework_MockObject_Invocation_Object $invocation, $methodName)
    {
        return $invocation->methodName === $methodName;
    }

    /**
     * @param \PHPUnit_Framework_MockObject_Invocation_Object $invocation
     * @param                                                 $args
     *
     * @return bool|mixed|void
     */
    private function compareArgs(\PHPUnit_Framework_MockObject_Invocation_Object $invocation, $args)
    {
        $parameters = $invocation->parameters;
        if (count($args) > count($parameters)) {
            return false;
        }
        $count = count($args);
        for ($i = 0; $i < $count; $i++) {
            $arg = $args[$i];
            $expected = $parameters[$i];
            if (!$this->compare($expected, $arg)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $expected
     * @param $arg
     *
     * @return bool|mixed
     */
    private function compare($expected, $arg)
    {
        if ($arg instanceof \PHPUnit_Framework_Constraint) {
            return $arg->evaluate($expected, '', true);
        } else {
            return $arg === $expected;
        }

    }

    public function wasNotCalledWith(array $args)
    {
        $funcArgs = func_get_args();
        $this->realWasCalledWithTimes($args, 0, $funcArgs);
    }

    /**
     * @param array $args
     * @param       $times
     * @param       $funcArgs
     */
    private function realWasCalledWithTimes(array $args, $times, $funcArgs)
    {
        $callTimes = $this->getCallTimesWithArgs($this->request->getMethodName(), $args);
        $this->matchCallWithTimes($args, $times, $this->request->getMethodName(), $callTimes);
    }

    public function wasCalledOnce()
    {
        $funcArgs = func_get_args();
        if ($this instanceof InstanceMethodCallVerifier) {
            $this->request->methodName = $funcArgs[0];
        }
        $this->realWasCalledTimes(1, $funcArgs);
    }

    public function wasCalledWithOnce(array $args)
    {
        $funcArgs = func_get_args();
        if ($this instanceof InstanceMethodCallVerifier) {
            $this->request->methodName = $funcArgs[1];
        }
        $this->realWasCalledWithTimes($args, 1, $funcArgs);
    }

    /**
     * Checks if the function or method was called the specified number
     * of times.
     *
     * @param  int $times
     *
     * @return void
     */
    public function wasCalledTimes($times)
    {
        $funcArgs = func_get_args();
        $this->realWasCalledTimes($times, $funcArgs);
    }

    /**
     * Checks if the function or method was called with the specified
     * arguments a number of times.
     *
     * @param  array $args
     * @param  int $times
     *
     * @return void
     */
    public function wasCalledWithTimes(array $args, $times)
    {
        $funcArgs = func_get_args();
        $this->realWasCalledWithTimes($args, $times, $funcArgs);
    }
}
