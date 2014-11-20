<?php

	namespace src\tad\FunctionMocker\Template\Wrapping;


	abstract class AbstractExtender implements Extender {

		/**
		 * @var string
		 */
		protected $wrappingClassName;

		public function getExtendingClass() {
			return $this->wrappingClassName;
		}

		public function getExtendingMethodsCode() {
			$signatures  = ${$this->wrappingClassName}::getInterfaceMethods();
			$methodsCode = array_map( function ( $signature ) {
				return sprintf( "public function %s{\n\t\$this->__functionMocker_extenderObject->%s;\n}", $signature, $signature );
			}, $signatures );

			return "\n\t" . implode( "\n\n\t", $methodsCode );
		}

	}