<?php

	namespace tad\FunctionMocker;


	class LessThanMatchingStrategy extends AbstractMatchingStrategy {

		public function matches( $times ) {
			return $times < $this->times;
		}

		public function __toString() {
			return sprintf( 'less than %d', $this->times );
		}
	}