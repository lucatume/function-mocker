<?php

	namespace tad\FunctionMocker;


	Abstract class AbstractMatchingStrategy implements MatchingStrategy {

		protected $times;

		public static function on( $times ) {
			\Arg::_( $times, 'Times' )->is_int();

			$instance        = new static();
			$instance->times = $times;

			return $instance;
		}

		public function matches( $times ) {
			throw new \RuntimeException('Method is not defined');
		}

		public function __toString(){
			throw new \RuntimeException('Method is not defined');
		}
	}