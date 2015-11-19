<?php

	namespace tad\FunctionMocker\Template; \Patchwork\Interceptor\deployQueue();

	use tad\FunctionMocker\Template\Extender\ExtenderInterface;

	class ClassTemplate {

		/**
		 * @var string
		 */
		protected $targetClass;

		/**
		 * @var \ReflectionClass
		 */
		protected $reflection;

		/**
		 * @var array
		 */
		protected $publicMethods;

		/**
		 * @var MethodCode
		 */
		protected $methodCode;

		/**
		 * @var ExtenderInterface
		 */
		protected $extender;

		public function setTargetClass( $className ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			$this->targetClass   = $className;
			$this->reflection    = new \ReflectionClass( $this->targetClass );
			$this->publicMethods = $this->reflection->getMethods( \ReflectionMethod::IS_PUBLIC );

			return $this;
		}

		public function getExtendedMockTemplate() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			return <<< CODESET
class %%extendedClassName%% extends %%mockClassName%% implements %%interfaceName%% {

	private \$__functionMocker_callHandler;
	private \$__functionMocker_originalMockObject;
	private \$__functionMocker_invokedRecorder;

	public function __set_functionMocker_callHandler(tad\FunctionMocker\Call\CallHandlerInterface \$callHandler){
		\$this->__functionMocker_callHandler = \$callHandler;
	}

	public function __get_functionMocker_CallHandler(){
		return \$this->__functionMocker_callHandler;
	}

	public function __set_functionMocker_originalMockObject(\PHPUnit_Framework_MockObject_MockObject \$mockObject){
		\$this->__functionMocker_originalMockObject = \$mockObject;
	}

	public function __set_functionMocker_invokedRecorder(\PHPUnit_Framework_MockObject_Matcher_InvokedRecorder \$invokedRecorder){
		\$this->__functionMocker_invokedRecorder = \$invokedRecorder;
	}

	public function __get_functionMocker_invokedRecorder(){
		return \$this->__functionMocker_invokedRecorder;
	}

	%%extendedMethods%%

	%%originalMethods%%
}
CODESET;
		}

		public function getExtendedMethodTemplate() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			return <<< CODESET
	public function %%call%%{
		call_user_func_array(array(\$this->__functionMocker_callHandler, '%%methodName%%'), func_get_args());
		return \$this;
	}

CODESET;

		}

		public function getMockTemplate( ExtenderInterface $wrapping ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);

			$vars = array(
				'extendedMethods' => $wrapping ? $this->extender->getExtendedMethodCallsAndNames() : '',
				'originalMethods' => $this->getOriginalMethodsCode()
			);

			$extendedMockTemplate = $this->getTemplate();
			array_walk( $vars, function ( $value, $key ) use ( &$extendedMockTemplate ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
				$extendedMockTemplate = preg_replace( '/%%' . $key . '%%/', $value, $extendedMockTemplate );
			} );

			$this->removeDoubleBlanksFrom( $extendedMockTemplate );

			return $extendedMockTemplate;
		}

		public function setMethodCode( MethodCode $methodCode ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			$this->methodCode = $methodCode;

			return $this;
		}

		private function getTemplate() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			return <<< CODESET
class %%extendedClassName%% extends %%mockClassName%% implements %%interfaceName%% {

	private \$__functionMocker_extenderObject;
	private \$__functionMocker_originalMockObject;

	public function __set_functionMocker_mockCallLogger(\$extenderObject){
		\$this->__functionMocker_extenderObject = \$extenderObject;
	}

	public function __set_functionMocker_originalMockObject(\PHPUnit_Framework_MockObject_MockObject \$mockObject){
		\$this->__functionMocker_originalMockObject = \$mockObject;
	}

	%%extendedMethods%%
	%%originalMethods%%

}

return true;
CODESET;
		}

		/**
		 * @return string
		 */
		protected function getOriginalMethodsCode() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			$methodsCode     = array_map( function ( $method ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
				return $this->methodCode->getMockCallingFrom( $method );
			}, $this->publicMethods );
			$originalMethods = "\n\t" . implode( "\n\n\t", $methodsCode );

			return $originalMethods;
		}


		/**
		 * @param $extendedMockTemplate
		 */
		protected function removeDoubleBlanksFrom( &$extendedMockTemplate ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			$extendedMockTemplate = preg_replace( "/^([\\t\\n]+)$/um", "", $extendedMockTemplate );
		}

		public function getExtendedSpyTemplate() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);

		}

		public function setExtender( ExtenderInterface $extender ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			$this->extender = $extender;
		}
	}\Patchwork\Interceptor\deployQueue();
