<?php
	namespace tad\FunctionMocker;

	use tad\FunctionMocker\AbstractMatchingStrategy;

	class EqualMatchingStrategy extends AbstractMatchingStrategy {

		public function matches( $times ) {
			return $this->times === $times;
		}

		public function __toString() {
			return sprintf( 'exactly %d', $this->times );
		}
	}