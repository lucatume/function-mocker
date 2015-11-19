<?php
 \Patchwork\Interceptor\deployQueue();/*
 * This file is part of the PHPUnit_MockObject package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Invocation matcher which does not care about previous state from earlier
 * invocations.
 *
 * This abstract class can be implemented by matchers which does not care about
 * state but only the current run-time value of the invocation itself.
 *
 * @since Class available since Release 1.0.0
 * @abstract
 */
abstract class PHPUnit_Framework_MockObject_Matcher_StatelessInvocation implements PHPUnit_Framework_MockObject_Matcher_Invocation
{
    /**
     * Registers the invocation $invocation in the object as being invoked.
     * This will only occur after matches() returns true which means the
     * current invocation is the correct one.
     *
     * The matcher can store information from the invocation which can later
     * be checked in verify(), or it can check the values directly and throw
     * and exception if an expectation is not met.
     *
     * If the matcher is a stub it will also have a return value.
     *
     * @param  PHPUnit_Framework_MockObject_Invocation $invocation
     *                                                             Object containing information on a mocked or stubbed method which
     *                                                             was invoked.
     * @return mixed
     */
    public function invoked(PHPUnit_Framework_MockObject_Invocation $invocation)
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
    }

    /**
     * Checks if the invocation $invocation matches the current rules. If it does
     * the matcher will get the invoked() method called which should check if an
     * expectation is met.
     *
     * @param  PHPUnit_Framework_MockObject_Invocation $invocation
     *                                                             Object containing information on a mocked or stubbed method which
     *                                                             was invoked.
     * @return bool
     */
    public function verify()
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
    }
}\Patchwork\Interceptor\deployQueue();
