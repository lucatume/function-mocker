<?php

namespace tad\FunctionMocker\Replacers; \Patchwork\Interceptor\deployQueue();


use tad\FunctionMocker\MockWrapper;
use tad\FunctionMocker\ReplacementRequest;
use tad\FunctionMocker\ReturnValue;

class InstanceForger {

	/**
	 * @var \PHPUnit_Framework_MockObject_Matcher_InvokedRecorder
	 */
	protected $invokedRecorder;
	/**
	 * @var \PHPUnit_Framework_TestCase
	 */
	private $testCase;

	public function getMock( ReplacementRequest $request, ReturnValue $returnValue ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		$className  = $request->getClassName();
		$methodName = $request->getMethodName();

		$methods = [ '__construct', $methodName ];

		$mockObject = $this->getPHPUnitMockObjectFor( $className, $methods );
		$this->setMockObjectExpectation( $mockObject, $methodName, $returnValue );

		$wrapperInstance = $this->getWrappedMockObject( $mockObject, $className, $request );

		return $wrapperInstance;
	}

	public function getPHPUnitMockObjectFor( $className, array $methods ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		$rc   = new \ReflectionClass( $className );
		$type = 100 * $rc->isInterface() + 10 * $rc->isAbstract() + $rc->isTrait();
		switch ( $type ) {
			case 110:
				// Interfaces will also be abstract classes
				$mockObject = $this->testCase->getMock( $className );
				break;
			case 10:
				// abstract class
				$mockObject = $this->testCase->getMockBuilder( $className )->disableOriginalConstructor()
				                             ->setMethods( $methods )->getMockForAbstractClass();
				break;
			case 11:
				// traits will also be abstract classes
				$mockObject = $this->testCase->getMockBuilder( $className )->disableOriginalConstructor()
				                             ->setMethods( $methods )->getMockForTrait();
				break;
			default:
				$mockObject = $this->testCase->getMockBuilder( $className )->disableOriginalConstructor()
				                             ->setMethods( $methods )->getMock();
				break;
		}

		return $mockObject;
	}

	public function setMockObjectExpectation( &$mockObject, $methodName, ReturnValue $returnValue = null ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		if ( $returnValue->isCallable() ) {
			// callback
			$mockObject->expects( $this->invokedRecorder )->method( $methodName )
			           ->willReturnCallback( $returnValue->getValue() );
		} else if ( $returnValue->isSelf() ) {
			// ->
			$mockObject->expects( $this->invokedRecorder )->method( $methodName )->willReturn( $mockObject );
		} else {
			// value
			$mockObject->expects( $this->invokedRecorder )->method( $methodName )
			           ->willReturn( $returnValue->getValue() );
		}
	}

	public function getWrappedMockObject( $mockObject, $className, ReplacementRequest $request ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		$mockWrapper = new MockWrapper();
		$mockWrapper->setOriginalClassName( $className );
		$wrapperInstance = $mockWrapper->wrap( $mockObject, $this->invokedRecorder, $request );

		return $wrapperInstance;
	}

	/**
	 * @param \PHPUnit_Framework_TestCase $testCase
	 */
	public function setTestCase( \PHPUnit_Framework_TestCase $testCase ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		$this->testCase        = $testCase;
		$this->invokedRecorder = $this->testCase->any();
	}
}\Patchwork\Interceptor\deployQueue();