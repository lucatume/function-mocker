<?php
namespace tad\FunctionMocker\Forge; \Patchwork\Interceptor\deployQueue();


use tad\FunctionMocker\Replacers\InstanceForger;
use tad\FunctionMocker\ReturnValue;

class Step implements StepInterface {
	/**
	 * @var string
	 */
	protected $class;

	/**
	 * @var InstanceForger
	 */
	protected $instanceForger;

	/**
	 * @var string[]
	 */
	protected $methods = [ ];

	public static function instance( $class ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		\Arg::_( $class, 'Class name' )->is_string()->assert( class_exists( $class ), 'Class to getMock must be defined' );

		$instance = new self;;
		$instance->class = $class;

		return $instance;
	}

	public function get() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		$methods    = array_merge( array_keys( $this->methods ), [ '__construct' ] );
		$mockObject = $this->instanceForger->getPHPUnitMockObjectFor( $this->class, $methods );

		foreach ( $this->methods as $method => $returnValue ) {
			$this->instanceForger->setMockObjectExpectation( $mockObject, $method, ReturnValue::from( $returnValue ) );
		}

		$request = InstanceMethodRequest::instance( $this->class );

		return $this->instanceForger->getWrappedMockObject( $mockObject, $this->class, $request );
	}

	/**
	 * @param InstanceForger $instanceForger
	 */
	public function setInstanceForger( InstanceForger $instanceForger ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		$this->instanceForger = $instanceForger;
	}

	public function setClass( $class ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		$this->class = $class;
	}

	public function method( $methodName, $returnValue = null ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		$this->methods[ $methodName ] = $returnValue;

		return $this;
	}
}\Patchwork\Interceptor\deployQueue();