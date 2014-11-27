<?php

	namespace tad\FunctionMocker\Template;

	class MethodCode {

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
		protected $methods;

		/** @var  string */
		protected $contents;

		public function setTargetClass( $targetClass ) {
			$this->targetClass = $targetClass;
			$this->reflection  = new \ReflectionClass( $targetClass );
			$this->methods     = $this->reflection->getMethods( \ReflectionMethod::IS_PUBLIC );
			$fileName          = $this->reflection->getFileName();
			if ( file_exists( $fileName ) ) {
				$this->contents = file_get_contents( $fileName );
			}

			return $this;
		}

		public function getTemplateFrom( $methodName ) {
			$body = '%%pre%% %%body%% %%post%%';

			return $this->getMethodCodeForWithBody( $methodName, $body );
		}

		public function getMockCallingFrom( $methodName ) {
			$code       = $this->getMethodCode( $methodName );
			$method     = is_a( $methodName, '\ReflectionMethod' ) ? $methodName : new \ReflectionMethod( $this->targetClass, $methodName );
			$methodName = is_string( $methodName ) ? $methodName : $method->name;
			$args       = array_map( function ( \ReflectionParameter $parameter ) {
				return '$' . $parameter->name;
			}, $method->getParameters() );
			$args       = implode( ', ', $args );
			$body       = "return \$this->__functionMocker_originalMockObject->$methodName($args);";

			return $this->getMethodCodeForWithBody( $methodName, $body );
		}

		/**
		 * @param $methodName
		 *
		 * @return array|string
		 */
		protected function getMethodCode( $methodName ) {

			$method = is_a( $methodName, '\ReflectionMethod' ) ? $methodName : new \ReflectionMethod( $this->targetClass, $methodName );

			$startLine = $method->getStartLine();
			$endLine   = $method->getEndLine();

			$lines = explode( "\n", $this->contents );
			$lines = array_map( function ( $line ) {
				return trim( $line );
			}, $lines );

			$code = array_splice( $lines, $startLine - 1, $endLine - $startLine + 1 );

			$code = implode( " ", $code );

			return $code;
		}

		/**
		 * @param $body
		 * @param $code
		 *
		 * @return mixed
		 */
		protected function replaceBody( $body, $code ) {
			$code = preg_replace( '/\\{.*\\}/', '{' . $body . '}', $code );
			$code = preg_replace( '/\\(\\s+/', '(', $code );
			$code = preg_replace( '/\\s+\\)/', ')', $code );

			return $code;
		}

		/**
		 * @param $methodName
		 *
		 * @return array|mixed|string
		 */
		protected function getMethodCodeForWithBody( $methodName, $body ) {
			$code = $this->getMethodCode( $methodName );

			$code = $this->replaceBody( $body, $code );

			return $code;
		}

		public function getAllMockCallings() {
			$code = array_map( function ( $method ) {
				return $this->getMockCallingFrom( $method );
			}, $this->methods );
			$code = implode( "\n\n\t", $code );

			return $code;
		}
	}
