<?php

namespace tad\FunctionMocker;

class UsageException extends Exception
{

    public static function becauseTheEnvDoesNotSpecifyABootstrapFile($env)
    {
        return new static("The {$env} environment is a folder but does not contain a bootstrap.php file; "
                          . "please create a bootstrap.php file for the {$env} environment.");
    }

    public static function becauseArrayDoesNotDefineAClassAndMethodCallable(array $function)
    {
        return new static(
            sprintf(
                'The array passed (%s) does not define a valid [Class::staticMethod] couple',
                json_encode($function)
            )
        );
    }

    public static function becauseClassAndMethodCoupleIsNotCallable($class, $method)
    {
        return new static(
            sprintf('The [%s, %s] array is not callable', $class, $method)
        );
    }
}
