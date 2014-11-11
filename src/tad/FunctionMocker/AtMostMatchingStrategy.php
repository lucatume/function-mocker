<?php

	namespace tad\FunctionMocker;


	class AtMostMatchingStrategy extends AbstractMatchingStrategy {

		public function matches( $times ) {
			return $times <= $this->times;
		}

		public function __toString(){
			return sprintf('at most %d', $this->times);
		}
	}