<?php

	namespace tad\FunctionMocker;

	class Checker {

		protected static $systemFunctions;
		protected        $functionName;
		protected        $isEvalCreated;

		public static function fromName( $functionName ) {
			if ( ! self::$systemFunctions ) {
				self::$systemFunctions = get_defined_functions()['internal'];
			}
			$condition = ! in_array( $functionName, self::$systemFunctions );
			\Arg::_( $functionName )->assert( $condition, 'Function must not be an internal one.' );

			$instance = new self;
			$instance->isEvalCreated = false;
			$instance->functionName = $functionName;
			$isMethod = preg_match( "/^[\\w\\d_\\\\]+::[\\w\\d_]+$/", $functionName );
			if ( ! $isMethod && ! function_exists( $functionName ) ) {
				$code = sprintf( 'function %s(){return null;}', $functionName );
				$ok = eval( $code );
				if ( $ok === false ) {
					throw new \Exception( "Could not eval code $code for function $functionName" );
				}
				$instance->isEvalCreated = true;
			}

			return $instance;
		}

		public function getFunctionName() {
			return $this->functionName;
		}

		public function isEvalCreated() {
			return $this->isEvalCreated;
		}
	}
