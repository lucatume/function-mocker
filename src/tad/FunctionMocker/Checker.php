<?php

	namespace tad\FunctionMocker;

	use Doctrine\Instantiator\Exception\InvalidArgumentException;

	class Checker {

		protected static $systemFuncs;
		protected        $functionName;
		protected        $isEvalCreated;

		public static function fromName( $functionName ) {
			if ( ! self::$systemFuncs ) {
				self::$systemFuncs = get_defined_functions()['internal'];
			}
			$condition = ! in_array( $functionName, self::$systemFuncs );
			\Arg::_( $functionName)->assert( $condition, 'Function must not be an internal one.' );

			$instance = new self;
			$instance->isEvalCreated = false;
			if ( ! function_exists( $functionName ) ) {
				eval( sprintf( 'function %s(){return null;}', $functionName ) );
				$instance->isEvalCreated = true;
			}
			$instance->functionName = $functionName;

			return $instance;
		}

		public function getFunctionName() {
			return $this->functionName;
		}

		public function isEvalCreated() {
			return $this->isEvalCreated;
		}
	}
