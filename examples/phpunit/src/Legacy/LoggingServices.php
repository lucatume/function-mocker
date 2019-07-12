<?php
/**
 * Handles additional logging services..
 *
 * @package Examples\PHPUnit\Legacy
 */
namespace Examples\PHPUnit\Legacy;

use Psr\Log\LoggerInterface;

/**
 * Class LoggingServices
 *
 * @package Examples\PHPUnit\Legacy
 */
class LoggingServices
{

    protected static $loggers = [
        ErrorLogLogger::class
    ];

    public static function getLoggers()
    {
        foreach (self::$loggers as &$logger) {
            if (!$logger instanceof LoggerInterface) {
                $logger = new $logger();
            }
        }

        return self::$loggers;
    }
}
