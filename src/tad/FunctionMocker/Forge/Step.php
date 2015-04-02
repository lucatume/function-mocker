<?php
namespace tad\FunctionMocker\Forge;


use tad\FunctionMocker\Replacers\InstanceForger;
use tad\FunctionMocker\ReturnValue;

class Step implements StepInterface {
	/**
	 * @var string
	 */
	protected $class;

	/**
	 * @var InstanceForger
	 */
	protected $instanceForger;

	/**
	 * @var string[]
	 */
	protected $methods = [ ];

	public static function instance( $class ) {
		\Arg::_( $class, 'Class name' )->is_string()->assert( class_exists( $class ), 'Class to getMock must be defined' );

		$instance = new self;;
		$instance->class = $class;

		return $instance;
	}

	public function get() {
		$methods    = array_merge( array_keys( $this->methods ), [ '__construct' ] );
		$mockObject = $this->instanceForger->getPHPUnitMockObjectFor( $this->class, $methods );

		foreach ( $this->methods as $method => $returnValue ) {
			$this->instanceForger->setMockObjectExpectation( $mockObject, $method, ReturnValue::from( $returnValue ) );
		}

		$request = InstanceMethodRequest::instance( $this->class );

		return $this->instanceForger->getWrappedMockObject( $mockObject, $this->class, $request );
	}

	/**
	 * @param InstanceForger $instanceForger
	 */
	public function setInstanceForger( InstanceForger $instanceForger ) {
		$this->instanceForger = $instanceForger;
	}

	public function setClass( $class ) {
		$this->class = $class;
	}

	public function method( $methodName, $returnValue = null ) {
		$this->methods[ $methodName ] = $returnValue;

		return $this;
	}
}