<?php
/**
 * Created by IntelliJ IDEA.
 * User: Luca
 * Date: 31/03/15
 * Time: 08:47
 */

namespace tests\tad\FunctionMocker\Forge;


use tad\FunctionMocker\Forge\Step;
use tad\FunctionMocker\Replacers\InstanceForger;

class StepTest extends \PHPUnit_Framework_TestCase {
	protected $class;

	public function setUp() {
		$this->class = $class = __NAMESPACE__ . '\StepDummyClass';
	}

	/**
	 * @test
	 * it should throw if passing a non string arg
	 */
	public function it_should_throw_if_passing_a_non_string_arg() {
		$this->setExpectedException( '\Exception' );
		Step::instance( 23 );
	}

	/**
	 * @test
	 * it should throw if the class name is a non existing class
	 */
	public function it_should_throw_if_the_class_name_is_a_non_existing_class() {
		$this->setExpectedException( '\Exception' );
		Step::instance( 'SomeUnrealClass' );
	}

	/**
	 * @test
	 * it should return a wrapped mock
	 */
	public function it_should_return_a_wrapped_mock() {
		$sut = new Step();
		$sut->setClass( $this->class );
		$this->set_instance_forger_on( $sut );

		$mock = $sut->get();

		$this->assertInstanceOf( 'tad\FunctionMocker\Call\Verifier\VerifierInterface', $mock );
	}

	/**
	 * @test
	 * it should allow defining methods to be replaced
	 */
	public function it_should_allow_defining_methods_to_be_replaced() {
		$sut = new Step( $this->class );
		$sut->setClass( $this->class );
		$this->set_instance_forger_on( $sut );

		$sut->method( 'methodOne' );
		$mock = $sut->get();

		$this->assertTrue( method_exists( $mock, 'methodOne' ) );
	}

	/**
	 * @test
	 * it should be able to mock more than one method
	 */
	public function it_should_be_able_to_mock_more_than_one_method() {
		$sut = new Step( $this->class );
		$sut->setClass( $this->class );
		$this->set_instance_forger_on( $sut );

		$sut->method( 'methodOne' );
		$sut->method( 'methodTwo' );
		$mock = $sut->get();

		$this->assertTrue( method_exists( $mock, 'methodOne' ) );
		$this->assertTrue( method_exists( $mock, 'methodTwo' ) );
	}

	public function primitiveReturnValues() {
		$_values = [
			23,
			'foo',
			new \stdClass(),
			array(),
			array( 'foo', 'baz' ),
			array( 'some' => 'value', 'foo' => 23 )
		];

		return array_map( function ( $val ) {
			return [ $val ];
		}, $_values );
	}

	/**
	 * @test
	 * it should be able to replace methods and set primitive return value
	 * @dataProvider primitiveReturnValues
	 */
	public function it_should_be_able_to_replace_methods_and_set_primitive_return_value( $exp ) {
		$sut = new Step( $this->class );
		$sut->setClass( $this->class );
		$this->set_instance_forger_on( $sut );

		$sut->method( 'methodOne', $exp );
		$mock = $sut->get();

		$this->assertEquals( $exp, $mock->methodOne() );
	}

	/**
	 * @test
	 * it should allow setting the return values of replaced methods to callback functions
	 */
	public function it_should_allow_setting_the_return_values_of_replaced_methods_to_callback_functions() {
		$sut = new Step( $this->class );
		$sut->setClass( $this->class );
		$this->set_instance_forger_on( $sut );
		$sut->method( 'methodOne', function () {
			return 23;
		} );
		$mock = $sut->get();

		$this->assertEquals( 23, $mock->methodOne() );
	}

	/**
	 * @test
	 * it should allow setting return values of replaced methods to callback functions and pass them same arguments as original methods
	 */
	public function it_should_allow_setting_return_values_of_replaced_methods_to_callback_functions_and_pass_them_same_arguments_as_original_methods() {
		$sut = new Step( $this->class );
		$sut->setClass( $this->class );
		$this->set_instance_forger_on( $sut );
		$sut->method( 'methodThree', function ( $one, $two ) {
			return $one + $two;
		} );
		$mock = $sut->get();

		$this->assertEquals( 23, $mock->methodThree( 1, 22 ) );
	}

	/**
	 * @param $sut
	 */
	private function set_instance_forger_on( $sut ) {
		$forger = new InstanceForger();
		$forger->setTestCase( $this );
		$sut->setInstanceForger( $forger );
	}
}

class StepDummyClass {
	public function methodOne() {

	}

	public function methodTwo() {

	}

	public function methodThree( $one, $two ) {

	}
}
