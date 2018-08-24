<?php
/**
 * Utility functions for the package.
 *
 * @package    FunctionMocker
 * @subpackage functions
 * @author     Luca Tumedei <luca@theaveragedev.com>
 * @copyright  2018 Luca Tumedei
 */

namespace tad\FunctionMocker;

/**
 * Creates a null returning function.
 *
 * This function is defined in a separate file from the one defining the FunctionMocker
 * class to allow Patchwork to include, and wrap, this file and, thus, the functions
 * here generated.
 *
 * @param string $functionName      The function name, not fully qualified.
 * @param string $functionNamespace The function namespace.
 *
 * @return boolean `true` if the function was created, `false` if the function was not created.
 * @throws \RuntimeException If the function could not be created.
 */
function createFunction($functionName, $functionNamespace = null) {
	$namespace = $functionNamespace ? " {$functionNamespace};" : '';
	$code = trim(sprintf('namespace %s {function %s(){return null;}}', $namespace, $functionName));
	// phpcs:ignore
	$ok = eval($code);
	if ($ok === false) {
		throw new \RuntimeException("Could not eval code $code for function $functionName");
	}

	return true;
}
