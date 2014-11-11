<?php

	namespace tad\FunctionMocker;


	class AtLeastMatchingStrategy extends AbstractMatchingStrategy {

		public function matches( $times ) {
			return $times >= $this->times;
		}

		public function __toString() {
			return sprintf( 'at least %d', $this->times );
		}
	}