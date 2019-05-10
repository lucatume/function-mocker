<?php

namespace tad\FunctionMocker\CLI;

use tad\FunctionMocker\CLI\Exceptions\RuntimeException;

class ExecutionTimeChecker
{

    protected $maxTime = - 1;

    protected $startTime = 0;

    public function __construct()
    {
        $this->maxTime = $this->getLimit();
    }

    /**
     * @return integer
     */
    public function getLimit()
    {
        $maxTime = ini_get('max_execution_time');

        if (empty($maxTime) || $maxTime <= 0) {
            $maxTime = - 1;
        }

        return $maxTime;
    }

    public function check()
    {
        $runningTime = ( microtime(true) - $this->startTime );
        if ($this->maxTime > 0 && $runningTime >= .9 * $this->maxTime) {
            throw RuntimeException::becauseTheCommandAlmostReachedTimeLimit();
        }
    }

    public function start()
    {
        $this->startTime = microtime(true);
    }
}
