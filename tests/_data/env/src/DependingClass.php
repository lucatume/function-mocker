<?php

namespace Acme\Company;


class DependingClass extends ParentClass {

	public function scalarArgs( array $arr, bool $bool, string $string, int $int, float $float ) {

	}

	public function method_using_global_class() {
		$o = new \GlobalImplementation;
	}

	public function method_using_relative_class() {
		$o = new SubPackage\Implementation;
	}

	public function method_using_fully_qualified_class() {
		$o = new \Other\Package\Implementation;
	}

	public function method_calling_global_function() {
		\global_function_one();
		global_function_two();
	}

	public function method_calling_relative_qualified_function() {
		utils\function_in_relative_namespace();
	}

	public function method_calling_namespace_function() {
		function_from_namespace();
	}

	protected function relative_dependency( Service\Db $db ) {

	}

	protected function fully_qualified_dependency( \Some\Other\Package\Foo $foo ) {

	}

	private function global_dependency( \Legacy_Db $db ) {

	}
}