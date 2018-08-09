<?php
namespace tad\FunctionMocker\CLI\Exceptions;

class BreakSignal extends \Exception
{

	public static function becauseThereAreNoMoreFunctionsOrClassesToFind() {
		return new self;
	}
}
