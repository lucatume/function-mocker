<?php

	namespace tad\FunctionMocker\MatchingStrategy; \Patchwork\Interceptor\deployQueue();

	class MatchingStrategyFactory {

		protected $times;
		protected $isGreaterThan;

		public static function make( $times ) {$__pwClosureName=__NAMESPACE__?__NAMESPACE__."\{closure}":"{closure}";$__pwClass=(__CLASS__&&__FUNCTION__!==$__pwClosureName)?__CLASS__:null;if(!empty(\Patchwork\Interceptor\State::$patches[$__pwClass][__FUNCTION__])){$__pwCalledClass=$__pwClass?\get_called_class():null;$__pwFrame=\count(\debug_backtrace(false));if(\Patchwork\Interceptor\intercept($__pwClass,$__pwCalledClass,__FUNCTION__,$__pwFrame,$__pwResult)){return$__pwResult;}}unset($__pwClass,$__pwCalledClass,$__pwResult,$__pwClosureName,$__pwFrame);
			\Arg::_( $times, 'Times' )->is_int()->_or()->is_string();

			if ( is_numeric( $times ) ) {
				$times = (int) $times;

				return EqualMatchingStrategy::on( $times );
			}

			$matches          = array();
			if ( ! preg_match( "/(>|>=|<|<=|==|!=)*(\\d)+/uU", $times, $matches ) ) {

				throw new \InvalidArgumentException( 'If times is a string it must follow the pattern [==|!=|<=|<|>=|>]*\d+' );
			}

			$prefix = $matches[1];
			$times  = (int) $matches[2];

			switch ( $prefix ) {
				case '>':
					$matchingStrategy = GreaterThanMatchingStrategy::on( $times );
					break;
				case '>=':
					$matchingStrategy = AtLeastMatchingStrategy::on( $times );
					break;
				case '==':
					$matchingStrategy = EqualMatchingStrategy::on( $times );
					break;
				case '<':
					$matchingStrategy = LessThanMatchingStrategy::on( $times );
					break;
				case '<=':
					$matchingStrategy = AtMostMatchingStrategy::on( $times );
					break;
				case '!=':
					$matchingStrategy = NotEqualMatchingStrategy::on( $times );
					break;
				default:
					$matchingStrategy = EqualMatchingStrategy::on( $times );
					break;
			}

			return $matchingStrategy;
		}
	}\Patchwork\Interceptor\deployQueue();
