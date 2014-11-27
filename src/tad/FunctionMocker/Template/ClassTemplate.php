<?php

	namespace tad\FunctionMocker\Template;

	use tad\FunctionMocker\Template\Extender\Extender;

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
		 * @var Extender
		 */
		protected $extender;

		public function setTargetClass( $className ) {
			$this->targetClass   = $className;
			$this->reflection    = new \ReflectionClass( $this->targetClass );
			$this->publicMethods = $this->reflection->getMethods( \ReflectionMethod::IS_PUBLIC );

			return $this;
		}

		public function getExtendedMockTemplate() {
			return <<< CODESET
class %%extendedClassName%% extends %%mockClassName%% implements %%interfaceName%% {

	private \$__functionMocker_callHandler;
	private \$__functionMocker_originalMockObject;
	private \$__functionMocker_invokedRecorder;

	public function __set_functionMocker_callHandler(tad\FunctionMocker\Call\CallHandler \$callHandler){
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

		public function getExtendedMethodTemplate() {
			return <<< CODESET
	%%signature%%{
		\$this->__functionMocker_callHandler->%%call%%;
		return \$this;
	}

CODESET;

		}

		public function getMockTemplate( Extender $wrapping ) {

			$vars = array(
				'extendedMethods' => $wrapping ? $this->extender->getExtenderMethodsSignaturesAndCalls() : '',
				'originalMethods' => $this->getOriginalMethodsCode()
			);

			$extendedMockTemplate = $this->getTemplate();
			array_walk( $vars, function ( $value, $key ) use ( &$extendedMockTemplate ) {
				$extendedMockTemplate = preg_replace( '/%%' . $key . '%%/', $value, $extendedMockTemplate );
			} );

			$this->removeDoubleBlanksFrom( $extendedMockTemplate );

			return $extendedMockTemplate;
		}

		public function setMethodCode( MethodCode $methodCode ) {
			$this->methodCode = $methodCode;

			return $this;
		}

		private function getTemplate() {
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
		protected function getOriginalMethodsCode() {
			$methodsCode     = array_map( function ( $method ) {
				return $this->methodCode->getMockCallingFrom( $method );
			}, $this->publicMethods );
			$originalMethods = "\n\t" . implode( "\n\n\t", $methodsCode );

			return $originalMethods;
		}


		/**
		 * @param $extendedMockTemplate
		 */
		protected function removeDoubleBlanksFrom( &$extendedMockTemplate ) {
			$extendedMockTemplate = preg_replace( "/^([\\t\\n]+)$/um", "", $extendedMockTemplate );
		}

		public function getExtendedSpyTemplate() {

		}

		public function setExtender( Extender $extender ) {
			$this->extender = $extender;
		}
	}
