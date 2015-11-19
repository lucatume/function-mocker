<?php
/**
 * Created by PhpStorm.
 * User: Luca
 * Date: 20/01/15
 * Time: 14:47
 */

namespace tad\FunctionMocker; \Patchwork\Interceptor\deployQueue();


class FunctionMockerTestWrappingTest extends \PHPUnit_Framework_TestCase {
    /**
     * @test
     * it should allow wrapping the test case and call its methods
     */
    public function it_should_allow_wrapping_the_test_case_and_call_its_methods()
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        FunctionMocker::setTestCase($this);

        $this->assertEquals(23 ,FunctionMocker::someMethod());
    }

    public function someMethod(){$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        return 23;
    }
}\Patchwork\Interceptor\deployQueue();
