<?php

	namespace tad\FunctionMocker\Template\Extender; \Patchwork\Interceptor\deployQueue();


	class SpyExtender extends AbstractExtender {

		protected $extenderClassName     = 'tad\FunctionMocker\Call\Verifier\InstanceMethodCallVerifier';
		protected $extenderInterfaceName = 'tad\FunctionMocker\Call\Verifier\VerifierInterface';

		public function getExtendedMethodCallsAndNames() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			return array(
				'wasCalledTimes($times)' => 'wasCalledTimes',
				'wasCalledWithTimes(array $args = array(), $times)' => 'wasCalledWithTimes',
				'wasNotCalled()' => 'wasNotCalled',
				'wasNotCalledWith(array $args = array())' => 'wasNotCalledWith',
				'wasCalledWithOnce(array $args = array())' => 'wasCalledWithOnce',
				'wasCalledOnce()' => 'wasCalledOnce'
			);
		}

	}\Patchwork\Interceptor\deployQueue();
