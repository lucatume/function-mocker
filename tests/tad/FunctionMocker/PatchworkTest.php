<?php

	namespace tad\FunctionMocker\Tests;


	class PatchworkTest extends \PHPUnit_Framework_TestCase {

		/**
		 * @test
		 * it should allow redefining an instance method
		 */
		public function it_should_allow_redefining_an_instance_method() {
			$a = new \AClass();
			\Patchwork\replace( array( $a, 'instanceMethod' ), function () {
				return 'some foo';
			} );

			$this->assertEquals( 'some foo', $a->instanceMethod() );
		}
	}
