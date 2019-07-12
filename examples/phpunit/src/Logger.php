<?php

namespace Examples\PHPUnit;

use Examples\PHPUnit\Legacy\LoggingServices;

class Logger
{

    public function log($message, $when = null)
    {
        $when = $when ? $when : time();

        // log messages on an hourly base
        $transient    = 'log_' . date('Y_m_d_H', $when);
        $hourly_log   = (array) get_transient($transient);

        $hourly_log[ date('i:s', $when) ] = $message;

        set_transient($transient, $hourly_log, DAY_IN_SECONDS);

        // If we have an external logging service then dispatch the message log there too.
        foreach (LoggingServices::getLoggers() as $externalLogger) {
            /** @var \Psr\Log\LoggerInterface $externalLogger */
            $externalLogger->debug($message);
        }
    }
}
