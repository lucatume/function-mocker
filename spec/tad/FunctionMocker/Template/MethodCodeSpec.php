<?php

	namespace spec\tad\FunctionMocker\Template;

	use PhpSpec\ObjectBehavior;
	use Prophecy\Argument;

	class MethodCodeSpec extends ObjectBehavior {

		function it_is_initializable() {
			$this->shouldHaveType( 'tad\FunctionMocker\Template\MethodCode' );
		}

		/**
		 * it returns proper method template for a public method with no args
		 */
		public function it_returns_proper_method_template_for_a_public_method_with_no_args() {
			$template = 'public function methodOne() {%%pre%% %%body%% %%post%%}';
			$this->setTargetClass( __NAMESPACE__ . '\walClass' )->getTemplateFrom( 'methodOne' )
			     ->shouldReturn( $template );
		}

		/**
		 * it returns proper method template for a public method with array args
		 */
		public function it_returns_proper_method_template_for_a_public_method_with_array_args() {

			$template = 'public function methodTwo($one, array $two, array $three = null, array $four = array(), $five = array(1, 2, 3), $six = array(\'one\' => 1, \'two\' => 2)) {%%pre%% %%body%% %%post%%}';
			$this->setTargetClass( __NAMESPACE__ . '\walClass' )->getTemplateFrom( 'methodTwo' )
			     ->shouldReturn( $template );
		}

		/**
		 * it returns proper method template for public method with body
		 */
		public function it_returns_proper_method_template_for_public_method_with_body() {
			$template = 'public function methodThree() {%%pre%% %%body%% %%post%%}';
			$this->setTargetClass( __NAMESPACE__ . '\walClass' )->getTemplateFrom( 'methodThree' )
			     ->shouldReturn( $template );
		}

		/**
		 * it returns proper method template when requesting parent calling method
		 */
		public function it_returns_proper_method_template_when_requesting_parent_calling_method() {
			$template = 'public function methodThree() {return $this->__functionMocker_originalMockObject->methodThree();}';
			$this->setTargetClass( __NAMESPACE__ . '\walClass' )->getMockCallingFrom( 'methodThree' )
			     ->shouldReturn( $template );
		}

		/**
		 * it return proper method template when requesting parent calling method for method with args
		 */
		public function it_return_proper_method_template_when_requesting_parent_calling_method_for_method_with_args() {
			$template = 'public function methodTwo($one, array $two, array $three = null, array $four = array(), $five = array(1, 2, 3), $six = array(\'one\' => 1, \'two\' => 2)) {return $this->__functionMocker_originalMockObject->methodTwo($one, $two, $three, $four, $five, $six);}';
			$this->setTargetClass( __NAMESPACE__ . '\walClass' )->getMockCallingFrom( 'methodTwo' )
			     ->shouldReturn( $template );
		}

	}


	class walClass {

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

		public function methodThree() {
			// foo = some
			$foo             = 'some';
			$this->something = 1;
		}
	}
