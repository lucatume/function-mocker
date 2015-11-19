<?php
 \Patchwork\Interceptor\deployQueue();/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Wrapper for PHP errors.
 *
 * @since Class available since Release 2.2.0
 */
class PHPUnit_Framework_Error extends PHPUnit_Framework_Exception
{
    /**
     * Constructor.
     *
     * @param string    $message
     * @param int       $code
     * @param string    $file
     * @param int       $line
     * @param Exception $previous
     */
    public function __construct($message, $code, $file, $line, Exception $previous = null)
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        parent::__construct($message, $code, $previous);

        $this->file  = $file;
        $this->line  = $line;
    }
}\Patchwork\Interceptor\deployQueue();
