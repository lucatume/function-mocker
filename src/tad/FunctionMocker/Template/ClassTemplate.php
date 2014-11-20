<?php

	namespace tad\FunctionMocker\Template;

	use tad\FunctionMocker\MockCallLogger;

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

		public function setTargetClass( $className ) {
			$this->targetClass   = $className;
			$this->reflection    = new \ReflectionClass( $this->targetClass );
			$this->publicMethods = $this->reflection->getMethods( \ReflectionMethod::IS_PUBLIC );

			return $this;
		}

		public function getExtendedMockTemplate() {
			return $this->getMockTemplate( true );
		}

		public function getMockTemplate( $extend = false ) {

			$vars = array(
				'extendedMethods' => $extend ? $this->getExtendedMethods() : '',
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
class %%extendedClassName%% extends %%mockClassName%% {

	private \$__functionMocker_mockCallLogger;
	private \$__functionMocker_originalMockObject;

	public function __set_functionMocker_mockCallLogger(\\tad\FunctionMocker\MockCallLogger \$logger){
		\$this->__functionMocker_mockCallLogger = \$logger;
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

		private function getExtendedMethods() {
			$signatures  = MockCallLogger::getInterfaceMethods();
			$methodsCode = array_map( function ( $signature ) {
				return sprintf( "public function %s{\n\t\$this->__functionMocker_mockCallLogger->%s;\n}", $signature, $signature );
			}, $signatures );

			return "\n\t" . implode( "\n\n\t", $methodsCode );
		}

		/**
		 * @param $extendedMockTemplate
		 */
		protected function removeDoubleBlanksFrom( &$extendedMockTemplate ) {
			$extendedMockTemplate = preg_replace( "/^([\\t\\n]+)$/um", "", $extendedMockTemplate );
		}
	}
