<?php

	namespace spec\tad\FunctionMocker\Template;

	use PhpSpec\ObjectBehavior;
	use Prophecy\Argument;
	use tad\FunctionMocker\MockCallLogger;
	use tad\FunctionMocker\Template\MethodCode;

	class ClassTemplateSpec extends ObjectBehavior {

		function it_is_initializable() {
			$this->shouldHaveType( 'tad\FunctionMocker\Template\ClassTemplate' );
		}

		/**
		 * it returns proper class template without extended methods
		 */
		public function it_returns_proper_class_template_without_extended_methods() {
			$template   = <<< CODESET
class %%extendedClassName%% extends %%mockClassName%% {

	private \$__functionMocker_mockCallLogger;
	private \$__functionMocker_originalMockObject;

	public function __set_functionMocker_mockCallLogger(\\tad\FunctionMocker\MockCallLogger \$logger){
		\$this->__functionMocker_mockCallLogger = \$logger;
	}

	public function __set_functionMocker_originalMockObject(\PHPUnit_Framework_MockObject_MockObject \$mockObject){
		\$this->__functionMocker_originalMockObject = \$mockObject;
	}

	public function methodOne() {return \$this->__functionMocker_originalMockObject->methodOne();}

	public function methodTwo(\$one, array \$two, array \$three = null, array \$four = array(), \$five = array(1, 2, 3), \$six = array('one' => 1, 'two' => 2)) {return \$this->__functionMocker_originalMockObject->methodTwo(\$one, \$two, \$three, \$four, \$five, \$six);}

}

return true;
CODESET;
			$className  = __NAMESPACE__ . '\Class23';
			$methodCode = new MethodCode();
			$methodCode->setTargetClass( $className );

			$this->setTargetClass( $className )->setMethodCode( $methodCode )->getMockTemplate()
			     ->shouldReturn( $template );
		}

		/**
		 * it returns proper class template with extended methods
		 */
		public function it_returns_proper_class_template_with_extended_methods() {
			$template   = <<< CODESET
class %%extendedClassName%% extends %%mockClassName%% {

	private \$__functionMocker_mockCallLogger;
	private \$__functionMocker_originalMockObject;

	public function __set_functionMocker_mockCallLogger(\\tad\FunctionMocker\MockCallLogger \$logger){
		\$this->__functionMocker_mockCallLogger = \$logger;
	}

	public function __set_functionMocker_originalMockObject(\PHPUnit_Framework_MockObject_MockObject \$mockObject){
		\$this->__functionMocker_originalMockObject = \$mockObject;
	}

	%%extendedMethods%%

	public function methodOne() {return \$this->__functionMocker_originalMockObject->methodOne();}

	public function methodTwo(\$one, array \$two, array \$three = null, array \$four = array(), \$five = array(1, 2, 3), \$six = array('one' => 1, 'two' => 2)) {return \$this->__functionMocker_originalMockObject->methodTwo(\$one, \$two, \$three, \$four, \$five, \$six);}

}

return true;
CODESET;
			$className  = __NAMESPACE__ . '\Class23';
			$methodCode = new MethodCode();
			$methodCode->setTargetClass( $className );

			$signatures  = MockCallLogger::getInterfaceMethods();
			$methodsCode = array_map( function ( $signature ) {
				return sprintf( "public function %s{\n\t\$this->__functionMocker_mockCallLogger->%s;\n}", $signature, $signature );
			}, $signatures );
			$methodsCode = implode( "\n\n\t", $methodsCode );

			$extendedTemplate = preg_replace( '/%%extendedMethods%%/', $methodsCode, $template );

			$this->setTargetClass( $className )->setMethodCode( $methodCode )->getExtendedMockTemplate()
			     ->shouldReturn( $extendedTemplate );
		}
	}


	class Class23 {

		public function methodOne() {
		}

		public function methodTwo(
			$one, array $two, array $three = null, array $four = array(), $five = array(
				1,
				2,
				3
			), $six = array( 'one' => 1, 'two' => 2 )
		) {

		}
	}
