<?php

	namespace tad\FunctionMocker;


	class InvocationTest extends \PHPUnit_Framework_TestCase {

		/**
		 * @test
		 * it should return an array of arguments
		 */
		public function it_should_return_an_array_of_arguments() {
			$stub = $this->getMock( __NAMESPACE__ . '\Dummy' );
			$stub->expects( $spy = $this->any() )->method( 'instanceMethod' );

			$stub->instanceMethod( 23 );

			$this->assertEquals( 23, $spy->getInvocations()[0]->parameters[0] );
		}
	}


	class Dummy {

		public function instanceMethod() {

		}
	}