<?php
namespace tad\FunctionMocker\Forge;


class Step implements StepInterface
{
    /**
     * @var string
     */
    protected $class;

    public function __construct($class)
    {
        \Arg::_($class, 'Class name')->is_string()->assert(class_exists($class), 'Class to forge must be defined');
        $this->class = $class;
    }
}