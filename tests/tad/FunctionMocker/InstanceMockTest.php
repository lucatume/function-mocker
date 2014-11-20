<?php

	namespace tests\tad\FunctionMocker;


	use tad\FunctionMocker\InstanceMock;

	class InstanceMockTest extends \PHPUnit_Framework_TestCase {

		/**
		 * @test
		 * it should forward calls to the mocked instance when calling methods on the mocked instance
		 */
		public function it_should_forward_calls_to_the_mocked_instance_when_calling_methods_on_the_mocked_instance() {
			$mock = $this->getMock( __NAMESPACE__ . '\InstanceMockTestClass' );
			$mock->expects( $this->once() )->method( 'someMethod' );
			$mockI = $this->getMock( '\PHPUnit_Framework_MockObject_Matcher_Invocation' );

			$sut = InstanceMock::from( $mockI, $mock );

			$sut->someMethod();
		}
	}


	class InstanceMockTestClass {

		public function someMethod() {
		}
	}