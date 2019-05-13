<?php

namespace Examples\phpspec;

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
    }
}
