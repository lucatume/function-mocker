<?php
/**
 * Logs to the PHP error log.
 *
 * @since   TBD
 * @package Examples\PHPUnit\Legacy
 */

namespace Examples\PHPUnit\Legacy;

use Psr\Log\LoggerInterface;

/**
 * Class ErrorLogLogger
 *
 * @package Examples\PHPUnit\Legacy
 */
class ErrorLogLogger implements LoggerInterface
{

    public function emergency($message, array $context = array())
    {
        $this->log('emergency', $message, $context);
    }

    public function alert($message, array $context = array())
    {
        $this->log('alert', $message, $context);
    }

    public function critical($message, array $context = array())
    {
        $this->log('critical', $message, $context);
    }

    public function error($message, array $context = array())
    {
        $this->log('error', $message, $context);
    }

    public function warning($message, array $context = array())
    {
        $this->log('warning', $message, $context);
    }

    public function notice($message, array $context = array())
    {
        $this->log('notice', $message, $context);
    }

    public function info($message, array $context = array())
    {
        $this->log('info', $message, $context);
    }

    public function debug($message, array $context = array())
    {
        $this->log('debug', $message, $context);
    }

    public function log($level, $message, array $context = array())
    {
        /** @noinspection ForgottenDebugOutputInspection */
        error_log(
            sprintf(
                '%s - %s - context: %s',
                strtoupper($level),
                $message,
                json_encode($context, JSON_PRETTY_PRINT)
            )
        );
    }
}
