<?php

namespace tad\FunctionMocker\Call\Verifier;


use PHPUnit\Framework\MockObject\Invocation;
use ReflectionClass;
use tad\FunctionMocker\Call\Logger\LoggerInterface;
use tad\FunctionMocker\ReturnValue;

class InstanceMethodCallVerifier extends AbstractVerifier {

	public $constraintClass;
	protected $returnValue;
	protected $callLogger;

	public static function from( ReturnValue $returnValue, LoggerInterface $callLogger ) {
		$instance              = new self;
		$instance->returnValue = $returnValue;
		$instance->callLogger  = $callLogger;

		return $instance;
	}

	public function wasNotCalled() {
		if ( $this instanceof InstanceMethodCallVerifier ) {
			$this->request->methodName = func_get_arg( 0 );
		}
		$this->realWasCalledTimes( 0 );
	}

	/**
	 * @param $times
	 */
	private function realWasCalledTimes( $times ) {
		$callTimes = $this->getCallTimesWithArgs( $this->request->methodName );
		$this->matchCallTimes( $times, $callTimes, $this->request->methodName );
	}

	/**
	 * @param array  $args
	 * @param string $methodName
	 *
	 * @return array
	 */
	protected function getCallTimesWithArgs( $methodName, ?array $args = null ) {
		$invocations = $this->getInvocations();
		$callTimes   = 0;
		array_map( function ( $invocation ) use ( &$callTimes, $args, $methodName ) {
			if ( is_array( $args ) ) {
				$callTimes += $this->compareName( $invocation, $methodName ) && $this->compareArgs( $invocation, $args );
			} else {
				$callTimes += $this->compareName( $invocation, $methodName );
			}
		}, $invocations );

		return $callTimes;
	}

	/**
	 * @return array
	 * @throws \ReflectionException
	 */
	private function getInvocations() {
		$reflectionClass       = new ReflectionClass( get_class( $this->invokedRecorder ) );
		$parentReflectionClass = $reflectionClass->getParentClass();

		$invocationsProperty = $parentReflectionClass->getProperty('invocations');
		$invocationsProperty->setAccessible( true );

		return $invocationsProperty->getValue( $this->invokedRecorder );
	}

	/**
	 * @param \PHPUnit_Framework_MockObject_Invocation_Object|Invocation $invocation
	 * @param                                                 $methodName
	 *
	 * @return bool
	 */
	private function compareName( $invocation, $methodName ) {
		$invokedMethodName = method_exists( $invocation, 'getMethodName' )
			? $invocation->getMethodName()
			: $invocation->methodName;

		return $invokedMethodName === $methodName;
	}

	/**
	 * @param \PHPUnit_Framework_MockObject_Invocation_Object|Invocation $invocation
	 * @param                                          $args
	 *
	 * @return bool|mixed|void
	 */
	private function compareArgs( $invocation, $args ) {
		$parameters = method_exists( $invocation, 'getParameters' )
			? $invocation->getParameters()
			: $invocation->parameters;

		if ( count( $args ) > count( $parameters ) ) {
			return false;
		}
		$count = count( $args );
		for ( $i = 0; $i < $count; $i ++ ) {
			$arg      = $args[ $i ];
			$expected = $parameters[ $i ];
			if ( ! $this->compare( $expected, $arg ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * @param $expected
	 * @param $arg
	 *
	 * @return bool|mixed
	 */
	private function compare( $expected, $arg ) {
		if ( $arg instanceof $this->constraintClass ) {
			return $arg->evaluate( $expected, '', true );
		} else {
			return $arg === $expected;
		}
	}

	public function wasNotCalledWith( array $args ) {
		if ( $this instanceof InstanceMethodCallVerifier ) {
			$this->request->methodName = func_get_arg( 1 );
		}
		$this->realWasCalledWithTimes( $args, 0 );
	}

	/**
	 * @param array $args
	 * @param       $times
	 */
	private function realWasCalledWithTimes( array $args, $times ) {
		$callTimes = $this->getCallTimesWithArgs( $this->request->methodName, $args );
		$this->matchCallWithTimes( $args, $times, $this->request->methodName, $callTimes );
	}

	public function wasCalledOnce() {
		if ( $this instanceof InstanceMethodCallVerifier ) {
			$this->request->methodName = func_get_arg( 0 );
		}
		$this->realWasCalledTimes( 1 );
	}

	public function wasCalledWithOnce( array $args ) {
		if ( $this instanceof InstanceMethodCallVerifier ) {
			$this->request->methodName = func_get_arg( 1 );
		}
		$this->realWasCalledWithTimes( $args, 1 );
	}

	/**
	 * Checks if the function or method was called the specified number
	 * of times.
	 *
	 * @param  int $times
	 *
	 * @return void
	 */
	public function wasCalledTimes( $times ) {
		if ( $this instanceof InstanceMethodCallVerifier ) {
			$this->request->methodName = func_get_arg( 1 );
		}
		$this->realWasCalledTimes( $times );
	}

	/**
	 * Checks if the function or method was called with the specified
	 * arguments a number of times.
	 *
	 * @param  array $args
	 * @param  int   $times
	 *
	 * @return void
	 */
	public function wasCalledWithTimes( array $args, $times ) {
		if ( $this instanceof InstanceMethodCallVerifier ) {
			$this->request->methodName = func_get_arg( 2 );
		}
		$this->realWasCalledWithTimes( $args, $times );
	}
}
