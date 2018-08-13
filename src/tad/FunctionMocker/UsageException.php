<?php

namespace tad\FunctionMocker;

class UsageException extends Exception
{

	public static function becauseTheEnvDoesNotSpecifyABootstrapFile($env) {
		return new static("The {$env} environment is a folder but does not contain a bootstrap.php file; please create a bootstrap.php file for the {$env} environment.");

	}
}
