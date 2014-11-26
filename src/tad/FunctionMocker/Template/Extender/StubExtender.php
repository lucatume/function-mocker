<?php

	namespace tad\FunctionMocker\Template\Extender;


	class StubExtender extends AbstractExtender {

		protected $extenderClassName     = 'tad\FunctionMocker\Call\Stub\StubCallHandler';
		protected $extenderInterfaceName = 'tad\FunctionMocker\Call\Stub\Stub';

		public function getExtenderMethodsSignaturesAndCalls() {
			return array();
		}

	}