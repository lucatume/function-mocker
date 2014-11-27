<?php

	namespace tad\FunctionMocker;

	use tad\FunctionMocker\Template\ClassTemplate;
	use tad\FunctionMocker\Template\Extender\SpyExtender;
	use tad\FunctionMocker\Template\MethodCode;

	class MockWrapper {

		/**
		 * @var \PHPUnit_Framework_MockObject_MockObject
		 */
		protected $wrappedObject;

		/**
		 * @var string
		 */
		protected $originalClassName;

		public function getWrappedObject() {
			return $this->wrappedObject;
		}

		public function wrap( \PHPUnit_Framework_MockObject_MockObject $mockObject, \PHPUnit_Framework_MockObject_Matcher_InvokedRecorder $invokedRecorder, ReplacementRequest $request ) {

			$extender = new SpyExtender();

			return $this->getWrappedInstance( $mockObject, $extender, $invokedRecorder, $request );
		}

		public function setOriginalClassName( $className ) {
			\Arg::_( $className, "Original class name" )->is_string()
			    ->assert( class_exists( $className ), 'Original class must be defined' );

			$this->originalClassName = $className;
		}


		/**
		 * @param \PHPUnit_Framework_MockObject_MockObject              $object
		 * @param                                                       $extender
		 *
		 * @param \PHPUnit_Framework_MockObject_Matcher_InvokedRecorder $invokedRecorder
		 * @param ReplacementRequest                                    $request
		 *
		 * @throws \Exception
		 * @internal param $extenderClassName
		 *
		 * @return mixed
		 */
		protected function getWrappedInstance( \PHPUnit_Framework_MockObject_MockObject $object, $extender, \PHPUnit_Framework_MockObject_Matcher_InvokedRecorder $invokedRecorder = null, ReplacementRequest $request = null ) {
			$mockClassName = get_class( $object );
			$extendClassName = sprintf( '%s_%s', uniqid( 'Extended_' ), $mockClassName );
			/** @noinspection PhpUndefinedMethodInspection */
			$extenderClassName = $extender->getExtenderClassName();

			if ( ! class_exists( $extendClassName ) ) {
				$classTemplate = new ClassTemplate();
				$template = $classTemplate->getExtendedMockTemplate();
				$methodCodeTemplate = $classTemplate->getExtendedMethodTemplate();

				/** @noinspection PhpUndefinedMethodInspection */
				$interfaceName = $extender->getExtenderInterfaceName();
				/** @noinspection PhpUndefinedMethodInspection */
				$extendedMethods = $extender->getExtenderMethodsSignaturesAndCalls();

				$extendedMethodsCode = array();
				array_walk( $extendedMethods, function ( $call, $signature ) use ( &$extendedMethodsCode, $methodCodeTemplate ) {
					$code = preg_replace( '/%%signature%%/', $signature, $methodCodeTemplate );
					$code = preg_replace( '/%%call%%/', $call, $code );
					$extendedMethodsCode[] = $code;
				} );
				$extendedMethodsCode = implode( "\n", $extendedMethodsCode );

				$methodCode = new MethodCode();
				$methodCode->setTargetClass( $this->originalClassName );
				$originalMethodsCode = $methodCode->getAllMockCallings();

				$classCode = preg_replace( '/%%extendedClassName%%/', $extendClassName, $template );
				$classCode = preg_replace( '/%%mockClassName%%/', $mockClassName, $classCode );
				$classCode = preg_replace( '/%%interfaceName%%/', $interfaceName, $classCode );
				$classCode = preg_replace( '/%%extenderClassName%%/', $extenderClassName, $classCode );
				$classCode = preg_replace( '/%%extendedMethods%%/', $extendedMethodsCode, $classCode );
				$classCode = preg_replace( '/%%originalMethods%%/', $originalMethodsCode, $classCode );

				$ok = eval( $classCode );

				if ( $ok === false ) {
					throw new \Exception( 'There was a problem evaluating the code' );
				}
			}

			$wrapperInstance = new $extendClassName;
			/** @noinspection PhpUndefinedMethodInspection */
			$wrapperInstance->__set_functionMocker_originalMockObject( $object );
			$callHandler = new $extenderClassName;
			if ( $invokedRecorder ) {
				/** @noinspection PhpUndefinedMethodInspection */
				$callHandler->setInvokedRecorder( $invokedRecorder );
				/** @noinspection PhpUndefinedMethodInspection */
				$wrapperInstance->__set_functionMocker_invokedRecorder( $invokedRecorder );
			}
			if ( $request ) {
				/** @noinspection PhpUndefinedMethodInspection */
				$callHandler->setRequest( $request );
			}
			/** @noinspection PhpUndefinedMethodInspection */
			$wrapperInstance->__set_functionMocker_callHandler( $callHandler );

			return $wrapperInstance;
		}
	}
