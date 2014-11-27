<?php

	namespace tad\FunctionMocker\Template\Extender;


	class SpyExtender extends AbstractExtender {

		protected $extenderClassName     = 'tad\FunctionMocker\Call\Verifier\InstanceMethodCallVerifier';
		protected $extenderInterfaceName = 'tad\FunctionMocker\Call\Verifier\Verifier';

		public function getExtenderMethodsSignaturesAndCalls() {
			return array(
				'public function wasCalledTimes( $times )'                            => 'wasCalledTimes( $times )',
				'public function wasCalledWithTimes( array $args = array(), $times )' => 'wasCalledWithTimes( $args, $times )',
				'public function wasNotCalled()'                                      => 'wasNotCalled()',
				'public function wasNotCalledWith( array $args = null )'              => 'wasNotCalledWith( $args )',
				'public function wasCalledWithOnce( array $args = null )'              => 'wasCalledWithOnce( $args )',
				'public function wasCalledOnce()'                                     => 'wasCalledOnce()'
			);
		}

	}