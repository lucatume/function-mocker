<?php
/**
 * The main Function Mocker class.
 *
 * @package    FunctionMocker
 * @subpackage CLI
 * @author     Luca Tumedei <luca@theaveragedev.com>
 * @copyright  2018 Luca Tumedei
 */

namespace tad\FunctionMocker\CLI\Exceptions;

class BreakSignal extends \Exception
{

    public static function becauseThereAreNoMoreFunctionsOrClassesToFind()
    {
        return new self;
    }
}
