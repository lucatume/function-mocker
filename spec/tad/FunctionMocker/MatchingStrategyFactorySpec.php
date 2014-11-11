<?php

	namespace spec\tad\FunctionMocker;

	use PhpSpec\ObjectBehavior;
	use Prophecy\Argument;

	class MatchingStrategyFactorySpec extends ObjectBehavior {

		function it_is_initializable() {
			$this->shouldHaveType( 'tad\FunctionMocker\MatchingStrategyFactory' );
		}

		/**
		 * it can build exact matching strategy
		 */
		public function it_can_build_exact_matching_strategy() {
			$this::make( 3 )->shouldHaveType( 'tad\FunctionMocker\EqualMatchingStrategy' );
		}

		/**
		 * it can build greater than matching strategy
		 */
		public function it_can_build_greater_than_matching_strategy() {
			$this::make( '>3' )->shouldHaveType( 'tad\FunctionMocker\GreaterThanMatchingStrategy' );
		}

		/**
		 * it can build at least matching strategy
		 */
		public function it_can_build_at_least_matching_strategy() {
			$this::make( '>=3' )->shouldHaveType( 'tad\FunctionMocker\AtLeastMatchingStrategy' );
		}


		/**
		 * it can build not equal matching strategy
		 */
		public function it_can_build_not_equal_matching_strategy() {
			$this::make( '!=3' )->shouldHaveType( 'tad\FunctionMocker\NotEqualMatchingStrategy' );
		}

		/**
		 * it can build less than matching strategy
		 */
		public function it_can_build_less_than_matching_strategy() {
			$this::make( '<3' )->shouldHaveType( 'tad\FunctionMocker\LessThanMatchingStrategy' );

		}

		/**
		 * it can build at most matching strategy
		 */
		public function it_can_build_at_most_matching_strategy() {
			$this::make( '<=4' )->shouldHaveType( 'tad\FunctionMocker\AtMostMatchingStrategy' );
		}

	}
