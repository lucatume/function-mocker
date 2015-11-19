<?php

	namespace tad\FunctionMocker; \Patchwork\Interceptor\deployQueue();

	class Checker {

		protected static $systemFunctions;
		protected        $functionName;
		protected        $isEvalCreated;

		public static function fromName( $functionName ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
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
				$namespace = self::hasnamespace( $functionName ) ? 'namespace ' . self::getnamespacefrom( $functionName ) . ";" : '';
				$functionName = self::hasNamespace( $functionName ) ? self::getFunctionNameFrom( $functionName ) : $functionName;
				$code = sprintf( '%sfunction %s(){return null;}', $namespace, $functionName );
				$ok = eval(\Patchwork\Preprocessor\preprocessForEval( $code ));
				if ( $ok === false ) {
					throw new \Exception( "Could not eval code $code for function $functionName" );
				}
				$instance->isEvalCreated = true;
			}

			return $instance;
		}

		/**
		 * @param $functionName
		 *
		 * @return bool
		 */
		private static function hasNamespace( $functionName ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			$namespaceElements = explode( '\\', $functionName );
			if ( count( $namespaceElements ) === 1 ) {
				return false;
			}

			return true;
		}

		/**
		 * @param $functionName
		 *
		 * @return string
		 */
		private static function getNamespaceFrom( $functionName ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			$namespaceElements = explode( '\\', $functionName );
			array_pop( $namespaceElements );

			return implode( '\\', $namespaceElements );
		}

		private static function getFunctionNameFrom( $functionName ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			$elems = explode( '\\', $functionName );

			return array_pop( $elems );
		}

		public function getFunctionName() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			return $this->functionName;
		}

		public function isEvalCreated() {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			return $this->isEvalCreated;
		}
	}\Patchwork\Interceptor\deployQueue();
