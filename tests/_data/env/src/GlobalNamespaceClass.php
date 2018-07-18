<?php

class GlobalNamespaceClass {

	/**
	 * @param string $c
	 */
	public function publicMethodWitDocBlock( $c ) {
	}

	public function publicMethodWithoutDocBlock( $c ) {
	}

	/**
	 * @param int $b
	 */
	protected function protected_method_with_doc_block( $b ) {
	}

	protected function protected_method_without_doc_block( $b ) {
	}

	/**
	 * @param array $a
	 */
	private function privateMethodWithDocBlock( array $a ) {
	}

	private function privateMethodWithoutDocBlock( array $a ) {
	}
}