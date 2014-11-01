<?php

	namespace tad\FunctionMocker;
	class FunctionMocker {

		public static function load() {
			$dir = __DIR__;
			while ( true ) {
				if ( file_exists( $dir . '/vendor' ) ) {
					$patchworkFile = $dir . "/vendor/antecedent/patchwork/Patchwork.php";
					require_once $patchworkFile;
					break;
				} else {
					$dir = dirname( $dir );
				}
			}
		}

		public static function unload() {
			\Patchwork\undoAll();
		}

		public static function mock( $functionName, $returnValue = null ) {
			\Arg::_( $functionName, 'Function name' )->is_string();

			$checker = Checker::fromName( $functionName );
			$returnValue = ReturnValue::from( $returnValue );
			$invocation = new Invocation();
			$mockObject = Matcher::__from( $checker, $returnValue, $invocation );

			if ( function_exists( '\Patchwork\replace' ) ) {
				\Patchwork\replace( $functionName, function () use ( $functionName, $returnValue, $invocation ) {
					$trace = debug_backtrace();
					$args = array_filter( $trace, function ( $stackLog ) use ( $functionName ) {
						$check = isset( $stackLog['args'] ) && is_array( $stackLog['args'] ) && $stackLog['function'] === $functionName;

						return $check ? true : false;
					} );
					$args = array_values($args);
					$args = isset( $args[0] ) ? $args[0]['args'] : array();
					$invocation->called( $args );

					return $returnValue->isCallable() ? $returnValue->call( $args ) : $returnValue->getValue();
				} );
			}

			return $mockObject;
		}
	}
