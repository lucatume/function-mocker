<?php

	namespace spec\tad\FunctionMocker;

	use PhpSpec\ObjectBehavior;
	use Prophecy\Argument;

	class ReturnValueSpec extends ObjectBehavior {

		protected $sutClass = 'tad\FunctionMocker\ReturnValue';

		function it_is_initializable() {
			$this->shouldHaveType( $this->sutClass );
		}

		/**
		 * it can be constructed from a callable
		 */
		public function it_can_be_constructed_from_a_callable() {
			$f = function () {
				return 'foo';
			};

			$this::from( $f )->shouldHaveType( $this->sutClass );
		}

		/**
		 * it allows checking if return value is callable
		 */
		public function it_allows_checking_if_return_value_is_callable() {
			$f = function () {
				return 'foo';
			};

			$this::from( $f )->isCallable()->shouldReturn( true );
		}

		/**
		 * it can be constructed from a value
		 */
		public function it_can_be_constructed_from_a_value() {
			$this::from( 'foo' )->isCallable()->shouldReturn( false );
			$this::from( 'baz' )->isValue()->shouldReturn( true );
		}

		/**
		 * it allows getting the return value
		 */
		public function it_allows_getting_the_return_value() {
			$this::from( 'foo' )->getValue()->shouldReturn( 'foo' );
			$f = function () {
				return 'foo';
			};
			$this::from( $f )->getValue()->shouldReturn( $f );
		}

		/**
		 * it allows checking if the return value is null
		 */
		public function it_allows_checking_if_the_return_value_is_null() {
			$this::from( null )->isNull()->shouldReturn( true );
			$this::from( 23 )->isNull()->shouldReturn( false );
		}

		/**
		 * it should allow getting the return value of the callback function
		 */
		public function it_should_allow_getting_the_return_value_of_the_callback_function() {
			$this->from( function ( $value1, $value2 ) {
				return $value1 + $value2;
			} )->call( [ 2, 3 ] )->shouldReturn( 5 );
		}
	}
