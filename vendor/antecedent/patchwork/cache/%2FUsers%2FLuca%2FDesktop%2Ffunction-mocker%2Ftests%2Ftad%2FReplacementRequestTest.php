<?php
namespace tests\tad; \Patchwork\Interceptor\deployQueue();


use tad\FunctionMocker\ReplacementRequest;

class ReplacementRequestTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     * it should throw if request is not a string
     */
    public function it_should_throw_if_request_is_not_a_string()
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        $this->setExpectedException('InvalidArgumentException');
        ReplacementRequest::on(23);
    }

    /**
     * @test
     * it should allow doing function requests
     */
    public function it_should_allow_doing_function_requests()
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        $sut = ReplacementRequest::on('someFunction');

        $this->assertTrue($sut->isFunction());
        $this->assertFalse($sut->isInstanceMethod());
        $this->assertFalse($sut->isMethod());
        $this->assertFalse($sut->isStaticMethod());
    }


    /**
     * @test
     * it should allow doing static method requests
     */
    public function it_should_allow_doing_static_method_requests()
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        $sut = ReplacementRequest::on(__NAMESPACE__ . '\RequestClassOne::methodOne');

        $this->assertTrue($sut->isMethod());
        $this->assertTrue($sut->isStaticMethod());
        $this->assertFalse($sut->isFunction());
        $this->assertFalse($sut->isInstanceMethod());
    }

    /**
     * @test
     * it should allow doing instance method requests
     */
    public function it_should_allow_doing_instance_method_requests()
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        $sut = ReplacementRequest::on(__NAMESPACE__ . '\RequestClassOne::methodTwo');

        $this->assertTrue($sut->isMethod());
        $this->assertTrue($sut->isInstanceMethod());
        $this->assertFalse($sut->isStaticMethod());
        $this->assertFalse($sut->isFunction());
    }

    /**
     * @test
     * it should allow doing instance methods requests using arrow symbol
     */
    public function it_should_allow_doing_instance_methods_requests_using_arrow_symbol()
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        $sut = ReplacementRequest::on(__NAMESPACE__ . '\RequestClassOne->methodTwo');

        $this->assertTrue($sut->isMethod());
        $this->assertTrue($sut->isInstanceMethod());
        $this->assertFalse($sut->isStaticMethod());
        $this->assertFalse($sut->isFunction());
    }

    /**
     * @test
     * it should throw if trying to make static method requests using the arrow symbol
     */
    public function it_should_throw_if_trying_to_make_static_method_requests_using_the_arrow_symbol()
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        $this->setExpectedException('InvalidArgumentException');
        $sut = ReplacementRequest::on(__NAMESPACE__ . '\RequestClassOne->methodOne');
    }

    /**
     * @test
     * it should return proper function name
     */
    public function it_should_return_proper_function_name()
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        $sut = ReplacementRequest::on('someFunction');
        $this->assertEquals('someFunction', $sut->getMethodName());
    }

    /**
     * @test
     * it should return proper function name for namespaced functions
     */
    public function it_should_return_proper_function_name_for_namespaced_functions()
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        $functionName = __NAMESPACE__ . '\someFunction';
        $sut = ReplacementRequest::on($functionName);
        $this->assertEquals($functionName, $sut->getMethodName());
    }

    /**
     * @test
     * it should return proper class and method name for static method replacement requests
     */
    public function it_should_return_proper_class_and_method_name_for_static_method_replacement_requests()
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        $sut = ReplacementRequest::on(__NAMESPACE__ . '\RequestClassOne::methodOne');
        $this->assertEquals(__NAMESPACE__ . '\RequestClassOne', $sut->getClassName());
        $this->assertEquals('methodOne', $sut->getMethodName());
    }

    /**
     * @test
     * it should return proper clas and method name for instance method replacement requests
     */
    public function it_should_return_proper_clas_and_method_name_for_instance_method_replacement_requests()
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
        $sut = ReplacementRequest::on(__NAMESPACE__ . '\RequestClassOne->methodTwo');
        $this->assertEquals(__NAMESPACE__ . '\RequestClassOne', $sut->getClassName());
        $this->assertEquals('methodTwo', $sut->getMethodName());
    }
}\Patchwork\Interceptor\deployQueue();

class RequestClassOne
{
    public static function methodOne()
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);

    }

    public function methodTwo()
    {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);

    }
}\Patchwork\Interceptor\deployQueue();
