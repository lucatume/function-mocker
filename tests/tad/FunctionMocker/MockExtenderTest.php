<?php

	namespace tad\FunctionMocker\Tests;

	use tad\FunctionMocker\MockExtender as MockExtender;

	class MockExtenderTest extends \PHPUnit_Framework_TestCase {

		/**
		 * @test
		 * it should return an extension of the passed object instance
		 */
		public function it_should_return_an_extension_of_the_passed_object_instance() {
			$this->assertInstanceOf( 'AClass', MockExtender::from('\AClass') );
		}

		/**
		 * @test
		 * it should return an instance with the same methods as the original instance
		 */
		public function it_should_return_an_instance_with_the_same_methods_as_the_original_instance() {
		}
	}