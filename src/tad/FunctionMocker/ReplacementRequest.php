<?php

namespace tad\FunctionMocker;

class ReplacementRequest
{

    /**
     * @var string
     */
    public $methodName;

    /**
     * @var bool
     */
    protected $isFunction;

    /**
     * @var bool
     */
    protected $isMethod;

    /**
     * @var bool
     */
    protected $isStaticMethod;

    /**
     * @var bool
     */
    protected $isInstanceMethod;

    /**
     * @var string
     */
    protected $requestClassName;

    public static function on($mockRequest)
    {
        \Arg::_($mockRequest, 'Function or method name')->is_string();
        $instance = new self;

        $instance->isMethod = preg_match("/^[\\w\\\\_]*(::|->)[\\w\\d_]+/um", $mockRequest) ? true : false;
        $instance->isFunction = !$instance->isMethod;
        if ($instance->isFunction) {
            $instance->isStaticMethod = false;
            $instance->isInstanceMethod = false;
            $instance->requestClassName = '';
            $instance->methodName = $mockRequest;
        } else {
            $request = preg_split('/(::|->)/', $mockRequest);
            $className = $request[0];
            $methodName = $request[1];
            $reflection = new \ReflectionMethod($className, $methodName);
            $instance->isInstanceMethod = !$reflection->isStatic();

            $instance->ensure_matching_symbol($mockRequest);

            $instance->isStaticMethod = $reflection->isStatic();
            $instance->requestClassName = $reflection->class;
            $instance->methodName = $reflection->name;
        }

        return $instance;
    }

    /**
     * @param $requestString
     */
    private function ensure_matching_symbol($requestString)
    {
        $m = [];
        preg_match('/(::|->)/', $requestString, $m);
        $symbol = $m[1];
        if ($symbol === '->' && !$this->isInstanceMethod()) {
            throw new \InvalidArgumentException('Request was for a static method but the \'->\' symbol was used; keep it clear.');
        }
    }

    public function isFunction()
    {
        return $this->isFunction;
    }

    public function isStaticMethod()
    {
        return $this->isMethod && $this->isStaticMethod;
    }

    public function isInstanceMethod()
    {
        return $this->isMethod && $this->isInstanceMethod;
    }

    public function isMethod()
    {
        return $this->isMethod;
    }

    public function getClassName()
    {
        return $this->requestClassName;
    }

    public function getMethodName()
    {
        return $this->methodName;
    }
}
