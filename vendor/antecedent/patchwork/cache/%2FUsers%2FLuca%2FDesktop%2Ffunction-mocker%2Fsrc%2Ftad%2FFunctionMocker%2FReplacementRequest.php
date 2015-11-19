<?php

namespace tad\FunctionMocker; \Patchwork\Interceptor\deployQueue();

class ReplacementRequest {

	/**
	 * @var string
	 */
	public $methodName;

	/**
	 * @var bool
	 */
	protected $isFunction;

	/**
	 * @var bool
	 */
	protected $isMethod;

	/**
	 * @var bool
	 */
	protected $isStaticMethod;

	/**
	 * @var bool
	 */
	protected $isInstanceMethod;

	/**
	 * @var string
	 */
	protected $requestClassName;

	/**
	 * @var bool
	 */
	protected $isClass;

	public static function on( $mockRequest ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		\Arg::_( $mockRequest, 'Function or method name' )->is_string();

		$type = self::getType( $mockRequest );

		return self::getInstanceForTypeAndRequest( $type, $mockRequest );
	}

	/**
	 * @param $mockRequest
	 *
	 * @return string
	 */
	private static function getType( $mockRequest ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		if ( class_exists( $mockRequest ) || interface_exists( $mockRequest ) || trait_exists( $mockRequest ) ) {
			return 'class';
		}
		if ( preg_match( "/^[\\w\\\\_]*(::|->)[\\w\\d_]+/um", $mockRequest ) ) {
			return 'method';
		}

		return 'function';
	}

	/**
	 * @param $type
	 * @param $mockRequest
	 *
	 * @return ReplacementRequest
	 */
	private static function getInstanceForTypeAndRequest( $type, $mockRequest ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		$instance = new self;
		switch ( $type ) {
			case 'class': {
				$instance->isFunction       = false;
				$instance->isClass          = true;
				$instance->isStaticMethod   = false;
				$instance->isMethod         = false;
				$instance->isInstanceMethod = false;
				$instance->requestClassName = $mockRequest;
				$instance->methodName       = false;
				break;
			}
			case 'method': {
				$request    = preg_split( '/(::|->)/', $mockRequest );
				$className  = $request[0];
				$methodName = $request[1];
				$reflection = new \ReflectionMethod( $className, $methodName );

				$instance->isFunction       = false;
				$instance->isClass          = false;
				$instance->isMethod         = true;
				$instance->isInstanceMethod = ! $reflection->isStatic();

				$instance->ensure_matching_symbol( $mockRequest );

				$instance->isStaticMethod   = $reflection->isStatic();
				$instance->requestClassName = $reflection->class;
				$instance->methodName       = $reflection->name;
				break;
			}
			case 'function': {
				$instance->isFunction       = true;
				$instance->isClass          = false;
				$instance->isMethod         = false;
				$instance->isStaticMethod   = false;
				$instance->isInstanceMethod = false;
				$instance->requestClassName = '';
				$instance->methodName       = $mockRequest;
				break;
			}
		}

		return $instance;
	}

	/**
	 * @param $requestString
	 */
	private function ensure_matching_symbol( $requestString ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		$m = [ ];
		preg_match( '/(::|->)/', $requestString, $m );
		$symbol = $m[1];
		if ( $symbol === '->' && ! $this->isInstanceMethod() ) {
			throw new \InvalidArgumentException( 'Request was for a static method but the \'->\' symbol was used; keep it clear.' );
		}
	}

	public function isInstanceMethod() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		return $this->isMethod && $this->isInstanceMethod;
	}

	public function isFunction() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		return $this->isFunction;
	}

	public function isStaticMethod() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		return $this->isMethod && $this->isStaticMethod;
	}

	public function isMethod() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		return $this->isMethod;
	}

	public function getClassName() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		return $this->requestClassName;
	}

	public function getMethodName() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		return $this->methodName;
	}

	public function isClass() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
		return $this->isClass;
	}
}\Patchwork\Interceptor\deployQueue();
