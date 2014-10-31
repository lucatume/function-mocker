<?php

	namespace tad\FunctionMocker;

	class ReturnValue {

		protected $value;
		protected $isCallable;
		protected $isValue;
		protected $isNull;

		public static function from( $returnValue = null ) {
			$instance = new self;
			$instance->value = $returnValue;
			$instance->isCallable = is_callable( $instance->value );
			$instance->isNull = is_null( $returnValue );
			$instance->isValue = ! ( $instance->isCallable || $instance->isNull );

			return $instance;
		}

		public function isCallable() {
			return $this->isCallable;
		}

		public function isValue() {
			return $this->isValue;
		}

		public function getValue() {
			return $this->value;
		}

		public function isNull() {
			return $this->isNull;
		}
	}
