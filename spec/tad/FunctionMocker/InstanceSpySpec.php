<?php

	namespace spec\tad\FunctionMocker;

	use PhpSpec\ObjectBehavior;
	use Prophecy\Argument;

	class InstanceSpySpec extends ObjectBehavior {

		function it_is_initializable() {
			$this->shouldHaveType( 'tad\FunctionMocker\InstanceSpy' );
		}

		/**
		 * it can be built from a PHPUnit invocation and a mock object
		 */
		public function it_can_be_built_from_a_php_unit_invocation_and_a_mock_object( \PHPUnit_Framework_MockObject_Invocation $invocation, SomeClass $object ) {
			$this::from( $invocation, $object )->shouldHaveType( 'tad\FunctionMocker\InstanceSpy' );
		}

		/**
		 * it will forward non spy calls to the object
		 */
		public function it_will_forward_non_spy_calls_to_the_object( \PHPUnit_Framework_MockObject_Invocation $invocation, SomeClass $object ) {
			$object->instanceMethod()->willReturn( 123 );
			$sut = $this::from( $invocation, $object );

			$sut->instanceMethod()->shouldReturn( 123 );
		}

		/**
		 * it will forward wasCalledTimes to the invocation object
		 */
		public function it_will_forward_was_called_times_to_the_invocation_object(\PHPUnit_Framework_MockObject_Invocation $invocation, SomeClass $object) {
			$invocations = array();
			$invocation->getInvocations()->willReturn($invocations);
		        }
	}
